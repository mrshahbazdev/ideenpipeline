<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show tenant dashboard
     */
    public function index(string $tenantId): View
    {
        // Get tenant from route parameter
        $tenant = Tenant::findOrFail($tenantId);
        
        // Get authenticated user
        $user = auth()->user();
        
        return view('tenant.dashboard', [
            'tenant' => $tenant,
            'user' => $user,
        ]);
    }
}