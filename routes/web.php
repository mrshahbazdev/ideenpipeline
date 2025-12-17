<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Tenant\Auth\LoginController,
    Tenant\Auth\RegisterController,
    TenantController,
    HomeController
};
use App\Http\Controllers\Tenant\{
    DashboardController,
    TeamsController,
    MyTeamsController,
    IdeasController,
    UserTeamsController  // Ye add karna hai
};
use App\Http\Controllers\Tenant\Admin\{
    UserManagementController,
    AnalyticsController,
    SettingsController
};

// Root redirect - old file se
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
})->name('home.old');

// Public home - new file se
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Authentication Routes - new file se
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Tenant Management (Admin only) - new file se
Route::middleware(['auth'])->group(function () {
    Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
});

// Tenant-specific routes - old aur new dono merge karna
Route::prefix('tenant/{tenantId}')
    ->middleware(['identify.tenant'])
    ->name('tenant.')
    ->group(function () {
        
        // Old file ke guest routes - ye add karna hai
        Route::middleware('guest')->group(function () {
            // Registration
            Route::get('/register', [\App\Http\Controllers\Tenant\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
            Route::post('/register', [\App\Http\Controllers\Tenant\Auth\RegisterController::class, 'register'])->name('register.post');
            
            // Login
            Route::get('/login', [\App\Http\Controllers\Tenant\Auth\LoginController::class, 'showLoginForm'])->name('login');
            Route::post('/login', [\App\Http\Controllers\Tenant\Auth\LoginController::class, 'login'])->name('login.post');
        });
        
        // Protected routes (auth) - new file se
        Route::middleware(['auth'])->group(function () {
            
            // Dashboard - new file se
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            
            // Logout - old aur new dono
            Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
            
            // My Teams (All Users) - old aur new merge
            Route::get('/my-teams', [MyTeamsController::class, 'index'])->name('my-teams');
            // Old file ke UserTeamsController routes
            Route::post('/my-teams/{team}/join', [UserTeamsController::class, 'joinTeam'])->name('my-teams.join');
            Route::post('/my-teams/{team}/leave', [UserTeamsController::class, 'leaveTeam'])->name('my-teams.leave');
            Route::post('/teams/switch/{team}', [UserTeamsController::class, 'switchTeam'])->name('teams.switch');
            // New file ke MyTeamsController routes
            Route::post('/teams/{team}/join', [MyTeamsController::class, 'join'])->name('teams.join');
            Route::post('/teams/{team}/leave', [MyTeamsController::class, 'leave'])->name('teams.leave');
            Route::post('/teams/{team}/switch', [MyTeamsController::class, 'switch'])->name('teams.switch');
            
            // Ideas (requires team membership) - old aur new merge
            Route::get('/ideas', [IdeasController::class, 'index'])->name('ideas.index');
            Route::get('/ideas/table', [IdeasController::class, 'table'])->name('ideas.table');
            Route::get('/ideas/create', [IdeasController::class, 'create'])->name('ideas.create');
            Route::post('/ideas', [IdeasController::class, 'store'])->name('ideas.store');
            Route::get('/ideas/{idea}', [IdeasController::class, 'show'])->name('ideas.show');
            Route::get('/ideas/{idea}/edit', [IdeasController::class, 'edit'])->name('ideas.edit');
            Route::put('/ideas/{idea}', [IdeasController::class, 'update'])->name('ideas.update');
            Route::post('/ideas/{idea}/status', [IdeasController::class, 'updateStatus'])->name('ideas.update-status');
            
            // Voting - old aur new
            Route::post('/ideas/{idea}/vote', [IdeasController::class, 'vote'])->name('ideas.vote');
            
            // Comments - old aur new
            Route::post('/ideas/{idea}/comments', [IdeasController::class, 'storeComment'])->name('ideas.comments.store');
            Route::delete('/ideas/{idea}/comments/{comment}', [IdeasController::class, 'deleteComment'])->name('ideas.comments.delete');
            
            // Teams Management (Admin only) - old aur new merge
            Route::middleware(['admin.only'])->group(function () {
                Route::get('/teams', [TeamsController::class, 'index'])->name('teams.index');
                Route::get('/teams/create', [TeamsController::class, 'create'])->name('teams.create');
                Route::post('/teams', [TeamsController::class, 'store'])->name('teams.store');
                Route::get('/teams/{team}', [TeamsController::class, 'show'])->name('teams.show');
                Route::get('/teams/{team}/edit', [TeamsController::class, 'edit'])->name('teams.edit');
                Route::put('/teams/{team}', [TeamsController::class, 'update'])->name('teams.update');
                Route::delete('/teams/{team}', [TeamsController::class, 'destroy'])->name('teams.destroy');
                // Old file ke member management routes
                Route::post('/teams/{team}/members', [TeamsController::class, 'addMember'])->name('teams.add-member');
                Route::delete('/teams/{team}/members/{user}', [TeamsController::class, 'removeMember'])->name('teams.remove-member');
                // New file ke member management routes
                Route::post('/teams/{team}/add-member', [TeamsController::class, 'addMember'])->name('teams.add-member.new');
                Route::delete('/teams/{team}/remove-member/{user}', [TeamsController::class, 'removeMember'])->name('teams.remove-member.new');
            });
            
            // Admin Routes - old aur new merge
            Route::middleware(['admin.only'])->prefix('admin')->name('admin.')->group(function () {
                
                // User Management - old aur new
                Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
                Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
                Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
                Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
                Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
                Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
                Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
                
                // Analytics - old aur new
                Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
                
                // Settings - old aur new
                Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
                Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
            });
            
            // Old file ke admin routes jo prefix ke bahar thay - ye add karna hai
            Route::middleware(['admin.only'])->group(function () {
                // Old file ke user management routes
                Route::get('/users', [UserManagementController::class, 'index'])->name('users.index.old');
                Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create.old');
                Route::post('/users', [UserManagementController::class, 'store'])->name('users.store.old');
                Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit.old');
                Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update.old');
                Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy.old');
                Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status.old');
                
                // Old file ke analytics
                Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.old');
                
                // Old file ke settings
                Route::get('/settings', [SettingsController::class, 'index'])->name('settings.old');
                Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update.old');
            });
        });
    });