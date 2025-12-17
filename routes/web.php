<?php

use App\Http\Controllers\Tenant\Auth\LoginController;
use App\Http\Controllers\Tenant\DashboardController;
use Illuminate\Support\Facades\Route;

// Home - show all tenants (no subdomain)
Route::get('/', function () {
    // Get current tenant from middleware
    $tenant = tenant();
    
    return view('welcome', [
        'tenant' => $tenant
    ]);
})->name('home');

// Tenant routes with middleware (works with or without subdomain)
Route::prefix('tenant/{tenantId}')->middleware(['identify.tenant'])->name('tenant.')->group(function () {
    
    // Login routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    
    // Protected routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    });
});