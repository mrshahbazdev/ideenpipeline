<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RegisterController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegistrationForm(string $tenantId): View
    {
        $tenant = Tenant::findOrFail($tenantId);
        
        if (!$tenant->isActive()) {
            abort(403, 'This tenant subscription has expired.');
        }

        return view('tenant.auth.register', [
            'tenant' => $tenant,
            'tenantId' => $tenantId,
        ]);
    }

    /**
     * Handle registration
     */
    public function register(Request $request, string $tenantId): RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);
        
        if (!$tenant->isActive()) {
            return back()->with('error', 'This tenant subscription has expired.');
        }

        // Validate
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255',
                function ($attribute, $value, $fail) use ($tenant) {
                    $exists = User::withoutGlobalScope('tenant')
                        ->where('tenant_id', $tenant->id)
                        ->where('email', $value)
                        ->exists();
                    
                    if ($exists) {
                        $fail('This email is already registered in this tenant.');
                    }
                },
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Create user with default "standard" role
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'standard', // Default role - admin can upgrade later
            'email_verified_at' => now(),
        ]);

        \Log::info('New tenant user registered', [
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => 'standard',
        ]);

        // Set tenant context
        session(['tenant_id' => $tenantId]);

        // Log in user
        Auth::login($user);

        // Redirect to dashboard
        return redirect()
            ->route('tenant.dashboard', ['tenantId' => $tenantId])
            ->with('success', 'Welcome ' . $user->name . '! Your account has been created. Admin can assign you specific roles if needed.');
    }
}