<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UpgradeController extends Controller
{
    public function index(): View
    {
        $tenant = tenant();
        
        // Generate upgrade URL pointing to main platform
        $upgradeUrl = config('platform.url') . '/login?' . http_build_query([
            'redirect' => 'upgrade',
            'subscription_id' => $tenant->platform_subscription_id,
            'tenant_id' => $tenant->id,
        ]);
        
        return view('tenant.upgrade', [
            'tenant' => $tenant,
            'upgradeUrl' => $upgradeUrl,
        ]);
    }
}