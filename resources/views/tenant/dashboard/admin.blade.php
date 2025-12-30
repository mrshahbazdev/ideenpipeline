<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Top Navigation -->
    @include('tenant.partials.nav')

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Success Alert -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Welcome Section -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">
                Welcome back, {{ $user->name }}! 👋
            </h2>
            <p class="text-gray-600 mt-2">
                Here's an overview of your {{ $tenant->subdomain }} innovation hub
            </p>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- Total Users Card -->
            <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-blue-100 text-sm font-medium uppercase tracking-wide">Total Users</p>
                        <p class="text-4xl font-bold mt-2">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                        <i class="fas fa-users text-3xl"></i>
                    </div>
                </div>
                <div class="flex items-center text-xs text-blue-100">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span>{{ $stats['admins'] }} Admin • {{ $stats['standard_users'] }} Standard</span>
                </div>
            </div>

            <!-- Developers Card -->
            <div class="stat-card bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-purple-100 text-sm font-medium uppercase tracking-wide">Developers</p>
                        <p class="text-4xl font-bold mt-2">{{ $stats['developers'] }}</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                        <i class="fas fa-code text-3xl"></i>
                    </div>
                </div>
                <div class="flex items-center text-xs text-purple-100">
                    <i class="fas fa-laptop-code mr-2"></i>
                    <span>Technical team members</span>
                </div>
            </div>

            <!-- Work-Bees Card -->
            <div class="stat-card bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-green-100 text-sm font-medium uppercase tracking-wide">Work-Bees</p>
                        <p class="text-4xl font-bold mt-2">{{ $stats['work_bees'] }}</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                        <i class="fas fa-user-friends text-3xl"></i>
                    </div>
                </div>
                <div class="flex items-center text-xs text-green-100">
                    <i class="fas fa-tasks mr-2"></i>
                    <span>Active team members</span>
                </div>
            </div>

            <!-- Standard Users Card -->
            <div class="stat-card bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-indigo-100 text-sm font-medium uppercase tracking-wide">Standard Users</p>
                        <p class="text-4xl font-bold mt-2">{{ $stats['standard_users'] }}</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                        <i class="fas fa-user text-3xl"></i>
                    </div>
                </div>
                <div class="flex items-center text-xs text-indigo-100">
                    <i class="fas fa-hourglass-half mr-2"></i>
                    <span>Pending role assignment</span>
                </div>
            </div>

        </div>

        <!-- Tenant Information Card -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-info-circle text-indigo-600 mr-3 text-2xl"></i>
                    Tenant Information
                </h3>
                <span class="px-4 py-2 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                    <i class="fas fa-check-circle mr-1"></i>Active
                </span>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Subdomain -->
                <div class="flex items-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-globe text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-blue-600 font-medium uppercase tracking-wide">Subdomain</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ $tenant->subdomain }}</p>
                    </div>
                </div>

                <!-- Admin Name -->
                <div class="flex items-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-user-shield text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-green-600 font-medium uppercase tracking-wide">Admin Name</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ $tenant->admin_name }}</p>
                    </div>
                </div>

                <!-- Admin Email -->
                <div class="flex items-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-envelope text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-purple-600 font-medium uppercase tracking-wide">Email</p>
                        <p class="text-sm font-bold text-gray-900 mt-1 truncate">{{ $tenant->admin_email }}</p>
                    </div>
                </div>

                <!-- Expiry Date -->
                <div class="flex items-center p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg">
                    <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-orange-600 font-medium uppercase tracking-wide">Expires</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">
                            {{ $tenant->expires_at ? $tenant->expires_at->format('M d, Y') : 'Never' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Package Info -->
            <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-box text-indigo-600 text-lg mr-3"></i>
                        <div>
                            <p class="text-xs text-gray-500">Package</p>
                            <p class="font-semibold text-gray-900">{{ $tenant->package_name }}</p>
                        </div>
                    </div>
                    @if($tenant->expires_at)
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Days Remaining</p>
                            <p class="font-semibold text-orange-600">
                                {{ $tenant->expires_at->diffInDays(now()) }} days
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Team Members Section -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-between">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-users-cog mr-3"></i>
                    Team Members Management
                </h3>
                <button class="px-4 py-2 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-gray-100 transition">
                    <i class="fas fa-plus mr-2"></i>Add Member
                </button>
            </div>
            
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Joined
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentUsers as $member)
                                <tr class="hover:bg-gray-50 transition">
                                    <!-- User Avatar & Name -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white
                                                {{ $member->role === 'admin' ? 'bg-red-500' : '' }}
                                                {{ $member->role === 'developer' ? 'bg-purple-500' : '' }}
                                                {{ $member->role === 'work-bee' ? 'bg-green-500' : '' }}
                                                {{ $member->role === 'standard' ? 'bg-blue-500' : '' }}
                                            ">
                                                {{ strtoupper(substr($member->name, 0, 1)) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $member->name }}
                                                </div>
                                                @if($member->id === $user->id)
                                                    <div class="text-xs text-gray-500">(You)</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Email -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $member->email }}</div>
                                    </td>

                                    <!-- Role Badge -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full inline-flex items-center {{ $member->getRoleBadgeClass() }}">
                                            <i class="fas {{ $member->getRoleIcon() }} mr-1"></i>
                                            {{ ucfirst(str_replace('-', ' ', $member->role)) }}
                                        </span>
                                    </td>

                                    <!-- Join Date -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                            {{ $member->created_at->format('M d, Y') }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $member->created_at->diffForHumans() }}
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <button class="text-indigo-600 hover:text-indigo-900 p-2 hover:bg-indigo-50 rounded transition">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if($member->id !== $user->id)
                                                <button class="text-red-600 hover:text-red-900 p-2 hover:bg-red-50 rounded transition">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                                            <p class="text-gray-500 text-lg font-medium">No team members yet</p>
                                            <p class="text-gray-400 text-sm mt-1">Start by inviting your first team member</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
       

    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    © {{ date('Y') }} {{ $tenant->subdomain }} • Innovation Pipeline
                </p>
                <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <a href="#" class="hover:text-indigo-600">Help</a>
                    <span>•</span>
                    <a href="#" class="hover:text-indigo-600">Support</a>
                    <span>•</span>
                    <a href="#" class="hover:text-indigo-600">Documentation</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
