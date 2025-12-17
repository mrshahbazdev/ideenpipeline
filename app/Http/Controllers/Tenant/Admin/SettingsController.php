<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    public function index(string $tenantId): View
    {
        $tenant = Tenant::findOrFail($tenantId);

        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        return view('tenant.admin.settings', compact('tenant'));
    }

    public function update(Request $request, string $tenantId): RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);

        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        // $validated = $request->validate([
        //     'subdomain' => ['required', 'string', 'max:50', 'unique:tenants,subdomain,' . $tenant->id],
        //     'status' => ['required', 'in:active,expired,suspended'],
        //     'expires_at' => ['nullable', 'date'],
        // ]);

        //$tenant->update($validated);

        \Log::info('Tenant settings updated', [
            'admin_id' => Auth::id(),
            'tenant_id' => $tenant->id,
        ]);

        return back()->with('success', 'Settings updated successfully!');
    }
}