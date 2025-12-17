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
        $middleware->web(append: [
            \App\Http\Middleware\ValidateSubdomain::class,
        ]);
        $middleware->alias([
            'platform.auth' => \App\Http\Middleware\VerifyPlatformToken::class,
            'tenant.check' => \App\Http\Middleware\CheckTenantStatus::class,
            'identify.tenant' => \App\Http\Middleware\IdentifyTenant::class,
            'validate.subdomain' => \App\Http\Middleware\ValidateSubdomain::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
