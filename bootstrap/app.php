<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;

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
            'admin.only' => \App\Http\Middleware\AdminOnly::class,
        ]);

        // Override default auth middleware
        $middleware->redirectGuestsTo(function ($request) {
            // Get tenant from request
            $tenant = $request->attributes->get('tenant');
            
            if ($tenant) {
                return route('tenant.login', ['tenantId' => $tenant->id]);
            }

            $tenantId = $request->route('tenantId');
            if ($tenantId) {
                return route('tenant.login', ['tenantId' => $tenantId]);
            }

            return route('home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle authentication exceptions for tenant routes
        $exceptions->render(function (AuthenticationException $e, $request) {
            if (!$request->expectsJson()) {
                if ($request->is('tenant/*')) {
                    $tenantId = $request->route('tenantId');
                    
                    if ($tenantId) {
                        return redirect()->guest(route('tenant.login', ['tenantId' => $tenantId]));
                    }
                }
                
                return redirect()->guest(route('home'));
            }
        });
    })
    ->create();