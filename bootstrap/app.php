<?php

use App\Http\Middleware\JsonMiddleware;
use App\Http\Middleware\OptionalAuthenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('api', [JsonMiddleware::class]);

        $middleware->alias([
            'optional.auth' => OptionalAuthenticate::class,
            'auth.api' => \App\Http\Middleware\Authenticate::class,
            'wants_json' => JsonMiddleware::class,
            'frontend.secret' => \App\Http\Middleware\FrontendSecret::class, // 👈 درستش اینه

        ]);    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
