<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class AbuseService extends BaseService
{
    public function report(object $request, User $account): JsonResponse
    {
        if (Report::create([
            'user_id' => $request->user()->id,
            'account_id' => $account->id,
            'content' => $request->content,
        ])) {
            Log::info(App::getLocale());

            return $this->successResponse(__('responses.reportSubmitted'));
        }

        return $this->errorResponse(__('responses.unknownError'));
    }
}
