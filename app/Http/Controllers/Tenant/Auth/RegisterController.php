<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/dashboard';

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
        $validator = $this->validator($request->all(), $tenant);
        
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create user
        $user = $this->create($request->all(), $tenant);

        // Log in user
        $this->guard()->login($user);

        return redirect()
            ->route('tenant.dashboard', ['tenantId' => $tenantId])
            ->with('success', 'Registration successful! Welcome to ' . $tenant->subdomain);
    }

    /**
     * Validator
     */
    protected function validator(array $data, Tenant $tenant): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255',
                // Email must be unique WITHIN this tenant only
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
            'role' => ['nullable', 'in:work-bee,developer'],
        ]);
    }

    /**
     * Create user
     */
    protected function create(array $data, Tenant $tenant): User
    {
        return User::create([
            'tenant_id' => $tenant->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'work-bee',
            'email_verified_at' => now(), // Auto-verify
        ]);
    }
}