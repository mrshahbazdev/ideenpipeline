<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// Tenant routes (accessed via subdomain.crm-tool.test)
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    'tenant.check',
])->group(function () {
    
    // Upgrade page (when subscription expired)
    Route::get('/upgrade', [App\Http\Controllers\Tenant\UpgradeController::class, 'index'])
        ->name('tenant.upgrade')
        ->withoutMiddleware('tenant.check');
    
    // Auth routes
    Route::get('/login', [App\Http\Controllers\Tenant\Auth\LoginController::class, 'showLoginForm'])
        ->name('tenant.login');
    Route::post('/login', [App\Http\Controllers\Tenant\Auth\LoginController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\Tenant\Auth\LoginController::class, 'logout'])
        ->name('tenant.logout');
    
    // Protected tenant routes
    Route::middleware('auth')->group(function () {
        Route::get('/', [App\Http\Controllers\Tenant\DashboardController::class, 'index'])
            ->name('tenant.dashboard');
        
        // Add your tenant-specific routes here
        // Examples:
        // Route::resource('customers', CustomerController::class);
        // Route::resource('invoices', InvoiceController::class);
        // Route::resource('reports', ReportController::class);
    });
});