<?php

use App\Exceptions\Handler;
use App\Http\Middleware\LanguageMiddleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(LanguageMiddleware::class);
        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'admin.status' =>\App\Http\Middleware\IsAdmin::class,
            'subAdmin.status' => \App\Http\Middleware\SubAdminMiddleware::class,
            'update.last_seen' => \App\Http\Middleware\UpdateLastSeen::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                // Check if the original cause was a ModelNotFoundException
                $previous = $e->getPrevious();
    
                if ($previous instanceof ModelNotFoundException) {
                    $modelName = class_basename($previous->getModel());
                    return response()->json([
                        'status' => 'error',
                        'message' => "{$modelName} not found",
                    ], 404);
                }
    
                // If it’s just a missing route
                return response()->json([
                    'status' => 'error',
                    'message' => 'Endpoint not found',
                    'data' => null
                ], 404);
            }
        });
    })->create();
