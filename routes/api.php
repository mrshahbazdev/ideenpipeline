<?php

use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\TenantController;
use Illuminate\Support\Facades\Route;

// Public health check
Route::get('/health', [HealthController::class, 'check']);

// Platform authenticated routes (from main platform)
Route::middleware('platform.auth')->prefix('tenants')->group(function () {
    Route::post('/create', [TenantController::class, 'create']);
    Route::post('/{tenantId}/activate', [TenantController::class, 'activate']);
    Route::post('/{tenantId}/deactivate', [TenantController::class, 'deactivate']);
    Route::get('/{tenantId}/status', [TenantController::class, 'status']);
    Route::patch('/{tenantId}/update', [TenantController::class, 'update']);
    Route::delete('/{tenantId}', [TenantController::class, 'destroy']);
    // 修改这里：移除多余的 'tenants/'
    Route::post('/{tenantId}/update-password', [TenantController::class, 'updatePassword']);
});

// Webhooks from platform
Route::post('/webhooks/subscription-updated', [WebhookController::class, 'subscriptionUpdated']);
Route::post('/webhooks/subscription-cancelled', [WebhookController::class, 'subscriptionCancelled']);
Route::post('/webhooks/payment-received', [WebhookController::class, 'paymentReceived']);