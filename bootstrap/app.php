<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Handle missing login route (default Laravel issue)
        $exceptions->renderable(function (\Symfony\Component\Routing\Exception\RouteNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Unauthorized - No token provided'], 401);
            }
        });

        //Handle Authentication exceptions (Laravel guard failures)
        $exceptions->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
        });

        //Handle Unauthorized HTTP exceptions
        $exceptions->renderable(function (UnauthorizedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Unauthorized - Invalid or expired token'], 401);
            }
        });

        //Handle JWT specific exceptions
        $exceptions->renderable(function (TokenExpiredException $e, $request) {
            return response()->json(['message' => 'Token has expired'], 401);
        });

        $exceptions->renderable(function (TokenInvalidException $e, $request) {
            return response()->json(['message' => 'Token is invalid'], 401);
        });

        $exceptions->renderable(function (JWTException $e, $request) {
            return response()->json(['message' => 'Token not provided'], 401);
        });
    })
    ->create();
