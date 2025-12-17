<?php

use App\Http\Controllers\Tenant\Auth\LoginController;
use App\Http\Controllers\Tenant\Auth\RegisterController;
use App\Http\Controllers\Tenant\DashboardController;
use Illuminate\Support\Facades\Route;

// Home - show all tenants or tenant-specific landing
Route::get('/', function () {
    // Try to get tenant from request attributes (set by middleware)
    $tenant = request()->attributes->get('tenant');
    
    // If no tenant, show general landing page
    if (!$tenant) {
        return view('welcome', [
            'tenant' => null,
            'isGeneralLanding' => true,
        ]);
    }
    
    // Show tenant-specific landing page
    return view('welcome', [
        'tenant' => $tenant,
        'isGeneralLanding' => false,
    ]);
})->name('home');

// Tenant routes with middleware (works with or without subdomain)
Route::prefix('tenant/{tenantId}')->middleware(['identify.tenant'])->name('tenant.')->group(function () {
    
    // Registration routes
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
    
    // Login routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    
    // Protected routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    });
});