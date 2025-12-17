<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Web middleware
        $middleware->web(append: [
            \App\Http\Middleware\ValidateSubdomain::class,
        ]);
        
        // Register aliases
        $middleware->alias([
            'auth.platform' => \App\Http\Middleware\AuthenticatePlatform::class,
            'identify.tenant' => \App\Http\Middleware\IdentifyTenant::class,
            'validate.subdomain' => \App\Http\Middleware\ValidateSubdomain::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();