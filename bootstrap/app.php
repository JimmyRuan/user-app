<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // make certain endpoints available without CSRF token in the development environment
        if (env('APP_ENV') === 'local') {
            $middleware->validateCsrfTokens(except: [
                '/users',   // for "users.store" endpoint
                '/users/*', //for "users.update" and "users.destroy" endpoints
            ]);
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (ModelNotFoundException $e, $request) {
            return response()->json([
                'message' => "Resource not found",
            ], Response::HTTP_NOT_FOUND);
        });

        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json([
                'message' => 'Resource not found',
            ], Response::HTTP_NOT_FOUND);
        });
    })->create();
