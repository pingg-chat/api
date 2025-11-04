<?php

declare(strict_types = 1);

use App\Http\Middleware\AuthenticateUserForRequest;
use App\Http\Middleware\CheckKey;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using:function (): void {
            Route::middleware(
                'api',
                CheckKey::class,
                AuthenticateUserForRequest::class
            )
                ->group(__DIR__ . '/../routes/api.php');
        },
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
