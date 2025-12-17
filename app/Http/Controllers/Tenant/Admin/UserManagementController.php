<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Tenant};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash};
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    /**
     * Show users list
     */
    public function index(string $tenantId): View
    {
        $tenant = Tenant::findOrFail($tenantId);
        
        // Only admin can access
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $users = User::where('tenant_id', $tenant->id)
            ->withCount(['teams', 'ideas', 'votes'])
            ->latest()
            ->get();

        $stats = [
            'total_users' => $users->count(),
            'active_users' => $users->where('is_active', true)->count(),
            'admins' => $users->where('role', 'admin')->count(),
            'developers' => $users->where('role', 'developer')->count(),
            'work_bees' => $users->where('role', 'work-bee')->count(),
            'standard' => $users->where('role', 'standard')->count(),
        ];

        return view('tenant.admin.users.index', compact('tenant', 'users', 'stats'));
    }

    /**
     * Show create user form
     */
    public function create(string $tenantId): View
    {
        $tenant = Tenant::findOrFail($tenantId);
        return view('tenant.admin.users.create', compact('tenant'));
    }

    /**
     * Store new user
     */
    public function store(Request $request, string $tenantId): RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'role' => ['required', 'in:admin,developer,work-bee,standard'],
            'is_active' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'tenant_id' => $tenant->id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        \Log::info('User created by admin', [
            'admin_id' => Auth::id(),
            'new_user_id' => $user->id,
            'tenant_id' => $tenant->id,
        ]);

        return redirect()
            ->route('tenant.admin.users.index', ['tenantId' => $tenantId])
            ->with('success', 'User created successfully!');
    }

    /**
     * Show edit user form
     */
    public function edit(string $tenantId, User $user): View
    {
        $tenant = Tenant::findOrFail($tenantId);
        
        // Can't edit users from other tenants
        if ($user->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized');
        }

        return view('tenant.admin.users.edit', compact('tenant', 'user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, string $tenantId, User $user): RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);

        if ($user->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            //'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', Password::defaults()],
            'role' => ['required', 'in:admin,developer,work-bee,standard'],
            'is_active' => ['boolean'],
        ]);

        $user->name = $validated['name'];
        //$user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->is_active = $request->boolean('is_active');

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        \Log::info('User updated by admin', [
            'admin_id' => Auth::id(),
            'user_id' => $user->id,
        ]);

        return redirect()
            ->route('tenant.admin.users.index', ['tenantId' => $tenantId])
            ->with('success', 'User updated successfully!');
    }

    /**
     * Delete user
     */
    public function destroy(string $tenantId, User $user): RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);

        if ($user->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized');
        }

        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete yourself!');
        }

        // Prevent deleting last admin
        if ($user->isAdmin()) {
            $adminCount = User::where('tenant_id', $tenant->id)
                ->where('role', 'admin')
                ->count();
            
            if ($adminCount <= 1) {
                return back()->with('error', 'Cannot delete the last admin!');
            }
        }

        $userName = $user->name;
        $user->delete();

        \Log::info('User deleted by admin', [
            'admin_id' => Auth::id(),
            'deleted_user_id' => $user->id,
            'deleted_user_name' => $userName,
        ]);

        return redirect()
            ->route('tenant.admin.users.index', ['tenantId' => $tenantId])
            ->with('success', "User '{$userName}' deleted successfully!");
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(string $tenantId, User $user): RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);

        if ($user->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'User status updated!');
    }
}