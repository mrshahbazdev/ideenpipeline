<?php

use App\Http\Controllers\Tenant\Auth\LoginController;
use App\Http\Controllers\Tenant\Auth\RegisterController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\TeamsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\UserTeamsController;
use App\Http\Controllers\Tenant\IdeasController;
use App\Http\Controllers\Tenant\Admin\{UserManagementController, AnalyticsController, SettingsController};
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

Route::prefix('tenant/{tenantId}')->middleware(['identify.tenant'])->name('tenant.')->group(function () {
    
    // Auth routes (guest)
    Route::middleware('guest')->group(function () {
        // Registration
        Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
        
        // Login
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    });
    
    // Protected routes (auth)
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Ideas (requires team membership)
        Route::get('/ideas', [IdeasController::class, 'index'])->name('ideas.index');
        Route::get('/ideas/table', [IdeasController::class, 'table'])->name('ideas.table');
        Route::get('/ideas/create', [IdeasController::class, 'create'])->name('ideas.create');
        Route::post('/ideas', [IdeasController::class, 'store'])->name('ideas.store');
        Route::get('/ideas/{idea}', [IdeasController::class, 'show'])->name('ideas.show');

        Route::get('/ideas/{idea}/edit', [IdeasController::class, 'edit'])->name('ideas.edit');
        Route::put('/ideas/{idea}', [IdeasController::class, 'update'])->name('ideas.update');
        Route::post('/ideas/{idea}/status', [IdeasController::class, 'updateStatus'])->name('ideas.update-status');
        Route::post('/ideas/{idea}/vote', [IdeasController::class, 'vote'])->name('ideas.vote');
        // Comments
        Route::post('/ideas/{idea}/comments', [IdeasController::class, 'storeComment'])->name('ideas.comments.store');
        Route::delete('/ideas/{idea}/comments/{comment}', [IdeasController::class, 'deleteComment'])->name('ideas.comments.delete');
        // Teams routes (Admin only)
        Route::middleware(['admin.only'])->group(function () {
            Route::get('/teams', [TeamsController::class, 'index'])->name('teams.index');
            Route::get('/teams/create', [TeamsController::class, 'create'])->name('teams.create');
            Route::post('/teams', [TeamsController::class, 'store'])->name('teams.store');
            Route::get('/teams/{team}', [TeamsController::class, 'show'])->name('teams.show');
            Route::get('/teams/{team}/edit', [TeamsController::class, 'edit'])->name('teams.edit');
            Route::put('/teams/{team}', [TeamsController::class, 'update'])->name('teams.update');
            Route::delete('/teams/{team}', [TeamsController::class, 'destroy'])->name('teams.destroy');
            Route::post('/teams/{team}/members', [TeamsController::class, 'addMember'])->name('teams.add-member');
            Route::delete('/teams/{team}/members/{user}', [TeamsController::class, 'removeMember'])->name('teams.remove-member');
            // User Management
            Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
            Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
            Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
            Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
            
            // Analytics
            Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
            
            // Settings
            Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
            Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
        });

        // User Teams (All authenticated users)
        Route::get('/my-teams', [UserTeamsController::class, 'index'])->name('my-teams');
        Route::post('/my-teams/{team}/join', [UserTeamsController::class, 'joinTeam'])->name('my-teams.join');
        Route::post('/my-teams/{team}/leave', [UserTeamsController::class, 'leaveTeam'])->name('my-teams.leave');
        Route::post('/teams/switch/{team}', [UserTeamsController::class, 'switchTeam'])->name('teams.switch');
        
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    });
});