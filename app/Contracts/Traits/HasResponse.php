<?php

namespace App\Contracts\Traits;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * API Service response trait
 */
trait HasResponse
{
    public function setActive(): void
    {
        if (Auth::check()) {
            auth()->user()->update([
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

    }

    public function successResponse(string $message = '', ?array $data = []): JsonResponse
    {
        $this->setActive();

        return response()->json(
            [
                'status' => 'success',
                'message' => trans($message),
                'data' => $data,
            ],
            Response::HTTP_OK
        );
    }

    public function errorResponse(string $message): JsonResponse
    {
        $this->setActive();

        return response()->json(
            [
                'status' => 'error',
                'message' => trans($message),
            ],
            Response::HTTP_FORBIDDEN
        );
    }

    public function viewResponse(string $view, array $data = []): View
    {
        return view($view, $data);
    }

    private function getData(array $data): array
    {
        return auth()->check() ? ['user' => new UserResource(User::find(auth()->user()->id)), ...$data] : $data;
    }
}
