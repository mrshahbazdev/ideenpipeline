<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Auth\LoginController,
    Auth\RegisterController,
    TenantController,
    HomeController
};
use App\Http\Controllers\Tenant\{
    DashboardController,
    TeamController,
    TeamsController,
    IdeasController
};
use App\Http\Controllers\Tenant\Admin\{
    UserManagementController,
    AnalyticsController,
    SettingsController
};

// Root redirect
Route::get('/', function () {
    return redirect()->route('home');
});

// Public home
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Tenant Management (Admin only)
Route::middleware(['auth'])->group(function () {
    Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
});

// Tenant-specific routes
Route::prefix('tenant/{tenantId}')
    ->middleware(['identify.tenant', 'auth'])
    ->name('tenant.')
    ->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Logout
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        
        // My Teams (All Users)
        Route::get('/my-teams', [MyTeamsController::class, 'index'])->name('my-teams');
        Route::post('/teams/{team}/join', [MyTeamsController::class, 'join'])->name('teams.join');
        Route::post('/teams/{team}/leave', [MyTeamsController::class, 'leave'])->name('teams.leave');
        Route::post('/teams/{team}/switch', [MyTeamsController::class, 'switch'])->name('teams.switch');
        
        // Teams Management (Admin only)
        Route::middleware(['admin.only'])->group(function () {
            Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
            Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
            Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
            Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit');
            Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
            Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
            Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
            Route::post('/teams/{team}/add-member', [TeamController::class, 'addMember'])->name('teams.add-member');
            Route::delete('/teams/{team}/remove-member/{user}', [TeamController::class, 'removeMember'])->name('teams.remove-member');
        });
        
        // Ideas (requires team membership)
        Route::get('/ideas', [IdeasController::class, 'index'])->name('ideas.index');
        Route::get('/ideas/table', [IdeasController::class, 'table'])->name('ideas.table');
        Route::get('/ideas/create', [IdeasController::class, 'create'])->name('ideas.create');
        Route::post('/ideas', [IdeasController::class, 'store'])->name('ideas.store');
        Route::get('/ideas/{idea}', [IdeasController::class, 'show'])->name('ideas.show');
        Route::get('/ideas/{idea}/edit', [IdeasController::class, 'edit'])->name('ideas.edit');
        Route::put('/ideas/{idea}', [IdeasController::class, 'update'])->name('ideas.update');
        Route::post('/ideas/{idea}/status', [IdeasController::class, 'updateStatus'])->name('ideas.update-status');
        
        // Voting
        Route::post('/ideas/{idea}/vote', [IdeasController::class, 'vote'])->name('ideas.vote');
        
        // Comments
        Route::post('/ideas/{idea}/comments', [IdeasController::class, 'storeComment'])->name('ideas.comments.store');
        Route::delete('/ideas/{idea}/comments/{comment}', [IdeasController::class, 'deleteComment'])->name('ideas.comments.delete');
        
        // Admin Routes
        Route::middleware(['admin.only'])->prefix('admin')->name('admin.')->group(function () {
            
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
    });