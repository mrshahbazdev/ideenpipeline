<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ToolController;
use App\Http\Controllers\User\SubscriptionController;
use App\Models\Subscription;

// Subdomain routes
Route::domain('{subdomain}.ideenpipeline.de')->group(function () {
    Route::middleware(['web'])->group(function () {
        Route::get('/', function ($subdomain) {
            // Validate subdomain
            $subscription = Subscription::where('subdomain', $subdomain)
                ->where('status', 'active')
                ->where('is_tenant_active', true)
                ->first();

            if (!$subscription) {
                abort(404, 'This subdomain does not exist or is inactive.');
            }

            // Redirect to tenant
            return redirect($subscription->package->tool->api_url . '/tenant/' . $subscription->tenant_id . '/login');
        })->name('subdomain.home');
    });
});

// Main domain routes
Route::domain('ideenpipeline.de')->group(function () {
    // Your existing routes...
    Route::get('/', function () {
        return view('welcome');
    });
    
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
        
        Route::get('/tools', [ToolController::class, 'index'])->name('tools.index');
        Route::get('/tools/{tool}', [ToolController::class, 'show'])->name('tools.show');
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
    });
});