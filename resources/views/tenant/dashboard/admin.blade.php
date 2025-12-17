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
                        ADMIN
                    </span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">
                        <i class="fas fa-user-shield mr-2"></i>{{ $user->name }}
                    </span>
                    <form method="POST" action="{{ route('tenant.logout', ['tenantId' => $tenant->id]) }}">
                        @csrf
                        <button class="text-sm text-red-600 hover:text-red-700">
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
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">
                Welcome back, {{ $user->name }}! 👋
            </h2>
            <p class="text-gray-600 mt-2">Here's what's happening in your innovation hub</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-2">
                    <i class="fas fa-code text-purple-500"></i> {{ $stats['developers'] }} Developers
                    <span class="mx-2">•</span>
                    <i class="fas fa-user text-green-500"></i> {{ $stats['work_bees'] }} Work-Bees
                </p>
            </div>

            <!-- Total Ideas -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Ideas</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_ideas'] }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-lightbulb text-green-600 text-2xl"></i>
                    </div>
                </div>
                <p class="text-sm text-green-600 mt-2">
                    <i class="fas fa-check-circle"></i> {{ $stats['approved_ideas'] }} Approved
                </p>
            </div>

            <!-- Pending Review -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending Review</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['pending_review'] }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                    </div>
                </div>
                <p class="text-sm text-yellow-600 mt-2">
                    <i class="fas fa-euro-sign"></i> {{ $stats['pending_pricing'] }} Awaiting Pricing
                </p>
            </div>

            <!-- Approved Budget -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Approved Budget</p>
                        <p class="text-3xl font-bold text-gray-900">€{{ number_format($stats['total_budget'], 0) }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-euro-sign text-purple-600 text-2xl"></i>
                    </div>
                </div>
                <p class="text-sm text-purple-600 mt-2">
                    <i class="fas fa-chart-line"></i> Total approved projects
                </p>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Recent Users -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-user-plus text-blue-600 mr-2"></i>Recent Users
                    </h3>
                </div>
                <div class="p-6">
                    @forelse($recentUsers as $recentUser)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-600 font-semibold">
                                        {{ substr($recentUser->name, 0, 1) }}
                                    </span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $recentUser->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $recentUser->email }}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                {{ $recentUser->role === 'admin' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $recentUser->role === 'developer' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $recentUser->role === 'work-bee' ? 'bg-green-100 text-green-800' : '' }}
                            ">
                                {{ ucfirst($recentUser->role) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No users yet</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Ideas -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>Recent Ideas
                    </h3>
                </div>
                <div class="p-6">
                    @forelse($recentIdeas as $idea)
                        <div class="py-3 border-b border-gray-100 last:border-0">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $idea->title }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        by {{ $idea->creator->name }} • {{ $idea->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $idea->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $idea->status === 'new' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $idea->status === 'pending_pricing' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                ">
                                    {{ ucfirst(str_replace('_', ' ', $idea->status)) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No ideas yet</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="#" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <i class="fas fa-users text-2xl mb-2"></i>
                <h4 class="font-semibold">Manage Users</h4>
                <p class="text-sm text-blue-100 mt-1">View and manage team members</p>
            </a>

            <a href="#" class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <i class="fas fa-lightbulb text-2xl mb-2"></i>
                <h4 class="font-semibold">View All Ideas</h4>
                <p class="text-sm text-green-100 mt-1">Access innovation pipeline</p>
            </a>

            <a href="#" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <i class="fas fa-cog text-2xl mb-2"></i>
                <h4 class="font-semibold">Settings</h4>
                <p class="text-sm text-purple-100 mt-1">Configure tenant settings</p>
            </a>
        </div>

    </div>

</body>
</html>
