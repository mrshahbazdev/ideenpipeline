<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('identify.tenant');
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm(string $tenantId)
    {
        // Get tenant
        $tenant = Tenant::findOrFail($tenantId);
        
        // Check if tenant is active
        if (!$tenant->isActive()) {
            abort(403, 'This tenant subscription has expired.');
        }

        return view('tenant.auth.register', [
            'tenant' => $tenant,
        ]);
    }

    /**
     * Handle registration
     */
    public function register(Request $request, string $tenantId)
    {
        // Get tenant
        $tenant = Tenant::findOrFail($tenantId);
        
        // Check if tenant is active
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
                    // Check if email exists in this tenant
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
            'role' => ['nullable', 'in:work-bee,developer'],
        ]);

        // Create user
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'work-bee',
            'email_verified_at' => now(), // Auto-verify
        ]);

        // Log in user
        Auth::login($user);

        // Redirect to dashboard
        return redirect()
            ->route('tenant.dashboard', ['tenantId' => $tenantId])
            ->with('success', 'Registration successful! Welcome to ' . $tenant->subdomain);
    }
}