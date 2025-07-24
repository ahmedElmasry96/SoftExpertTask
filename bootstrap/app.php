<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*') || $request->wantsJson();
        });

        $exceptions->render(function (AuthenticationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], Response::HTTP_UNAUTHORIZED);
        });

        $exceptions->render(function (AccessDeniedHttpException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        });

        $exceptions->render(function (NotFoundHttpException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Not Found',
            ], Response::HTTP_NOT_FOUND);
        });
    })->create();
