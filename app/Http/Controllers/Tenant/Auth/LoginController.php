<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm(string $tenantId): View
    {
        $tenant = Tenant::findOrFail($tenantId);
        
        return view('tenant.login', [
            'tenant' => $tenant,
            'tenantId' => $tenantId,
        ]);
    }

    /**
     * Handle login
     */
    public function login(Request $request, string $tenantId): RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);
        
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Set tenant context in session first
        session(['tenant_id' => $tenantId]);

        // Attempt authentication with tenant scope
        // The global scope will automatically filter by tenant_id
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            \Log::info('Tenant login successful', [
                'tenant_id' => $tenantId,
                'email' => $credentials['email'],
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('tenant.dashboard', ['tenantId' => $tenantId]);
        }

        \Log::warning('Tenant login failed', [
            'tenant_id' => $tenantId,
            'email' => $credentials['email'],
        ]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request, string $tenantId): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->forget('tenant_id');

        return redirect()->route('tenant.login', ['tenantId' => $tenantId]);
    }
}