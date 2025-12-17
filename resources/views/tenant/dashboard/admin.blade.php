<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-indigo-600">
                        <i class="fas fa-building mr-2"></i>{{ $tenant->subdomain }}
                    </h1>
                    <span class="ml-3 px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                        <i class="fas fa-crown mr-1"></i>ADMIN
                    </span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">
                        <i class="fas fa-user-shield mr-2"></i>{{ $user->name }}
                    </span>
                    <form method="POST" action="{{ route('tenant.logout', ['tenantId' => $tenant->id]) }}">
                        @csrf
                        <button class="text-sm text-red-600 hover:text-red-700 font-medium">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Welcome Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">
                Welcome back, {{ $user->name }}! 👋
            </h2>
            <p class="text-gray-600 mt-2">Manage your innovation hub from here</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Users</p>
                        <p class="text-4xl font-bold mt-2">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-full">
                        <i class="fas fa-users text-3xl"></i>
                    </div>
                </div>
            </div>

            <!-- Developers -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Developers</p>
                        <p class="text-4xl font-bold mt-2">{{ $stats['developers'] }}</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-full">
                        <i class="fas fa-code text-3xl"></i>
                    </div>
                </div>
            </div>

            <!-- Work-Bees -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Work-Bees</p>
                        <p class="text-4xl font-bold mt-2">{{ $stats['work_bees'] }}</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-full">
                        <i class="fas fa-user-friends text-3xl"></i>
                    </div>
                </div>
            </div>

            <!-- Admins -->
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">Admins</p>
                        <p class="text-4xl font-bold mt-2">{{ $stats['admins'] }}</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-full">
                        <i class="fas fa-crown text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tenant Info Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-info-circle text-indigo-600 mr-2"></i>Tenant Information
            </h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="flex items-center p-3 bg-gray-50 rounded">
                    <i class="fas fa-building text-indigo-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-xs text-gray-500">Subdomain</p>
                        <p class="font-semibold">{{ $tenant->subdomain }}.ideenpipeline.de</p>
                    </div>
                </div>
                <div class="flex items-center p-3 bg-gray-50 rounded">
                    <i class="fas fa-user text-green-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-xs text-gray-500">Admin Name</p>
                        <p class="font-semibold">{{ $tenant->admin_name }}</p>
                    </div>
                </div>
                <div class="flex items-center p-3 bg-gray-50 rounded">
                    <i class="fas fa-envelope text-blue-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-xs text-gray-500">Admin Email</p>
                        <p class="font-semibold">{{ $tenant->admin_email }}</p>
                    </div>
                </div>
                <div class="flex items-center p-3 bg-gray-50 rounded">
                    <i class="fas fa-calendar text-purple-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-xs text-gray-500">Expires</p>
                        <p class="font-semibold">{{ $tenant->expires_at ? $tenant->expires_at->format('M d, Y') : 'Never' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user-plus text-blue-600 mr-2"></i>Team Members
                </h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">User</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Email</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Role</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $member)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold text-white
                                                {{ $member->role === 'admin' ? 'bg-red-500' : '' }}
                                                {{ $member->role === 'developer' ? 'bg-purple-500' : '' }}
                                                {{ $member->role === 'work-bee' ? 'bg-green-500' : '' }}
                                            ">
                                                {{ strtoupper(substr($member->name, 0, 1)) }}
                                            </div>
                                            <span class="ml-3 font-medium">{{ $member->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ $member->email }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                                            {{ $member->role === 'admin' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $member->role === 'developer' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $member->role === 'work-bee' ? 'bg-green-100 text-green-800' : '' }}
                                        ">
                                            <i class="fas 
                                                {{ $member->role === 'admin' ? 'fa-crown' : '' }}
                                                {{ $member->role === 'developer' ? 'fa-code' : '' }}
                                                {{ $member->role === 'work-bee' ? 'fa-user' : '' }}
                                                mr-1
                                            "></i>
                                            {{ ucfirst(str_replace('-', ' ', $member->role)) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-600">
                                        {{ $member->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-gray-500">
                                        No users yet
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
