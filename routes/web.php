<?php

use App\Http\Controllers\Tenant\Auth\LoginController;
use App\Http\Controllers\Tenant\Auth\RegisterController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\TeamsController;
use Illuminate\Support\Facades\Route;

// Home - show all tenants or tenant-specific landing
Route::get('/', function () {
    $tenant = request()->attributes->get('tenant');
    
    if (!$tenant) {
        return view('welcome', [
            'tenant' => null,
            'isGeneralLanding' => true,
        ]);
    }
    
    return view('welcome', [
        'tenant' => $tenant,
        'isGeneralLanding' => false,
    ]);
})->name('home');

// Tenant routes with middleware
Route::prefix('tenant/{tenantId}')->middleware(['identify.tenant'])->name('tenant.')->group(function () {
    
    // Guest routes
    Route::middleware('guest')->group(function () {
        // Registration
        Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
        
        // Login
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    });
    
    // Protected routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // Teams routes (Admin only - checked in controller)
        Route::get('/teams', [TeamsController::class, 'index'])->name('teams.index');
        Route::get('/teams/create', [TeamsController::class, 'create'])->name('teams.create');
        Route::post('/teams', [TeamsController::class, 'store'])->name('teams.store');
        Route::get('/teams/{team}', [TeamsController::class, 'show'])->name('teams.show');
        Route::get('/teams/{team}/edit', [TeamsController::class, 'edit'])->name('teams.edit');
        Route::put('/teams/{team}', [TeamsController::class, 'update'])->name('teams.update');
        Route::delete('/teams/{team}', [TeamsController::class, 'destroy'])->name('teams.destroy');
        Route::post('/teams/{team}/members', [TeamsController::class, 'addMember'])->name('teams.add-member');
        Route::delete('/teams/{team}/members/{user}', [TeamsController::class, 'removeMember'])->name('teams.remove-member');
            // Logout
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    });
});