<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            $modelName = class_basename($exception->getModel());
            return response()->json([
                'status' => 'error',
                'message' => "{$modelName} data not found",
                'data' => null
            ], 404);
        }

        return parent::render($request, $exception);
    }
}

