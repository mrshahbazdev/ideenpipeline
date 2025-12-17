<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ $tenant->subdomain }}</title>
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
                        <i class="fas fa-lightbulb mr-2"></i>{{ $tenant->subdomain }}
                    </h1>
                    <span class="ml-3 px-3 py-1 text-xs font-semibold rounded-full
                        {{ $user->role === 'developer' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}
                    ">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">
                        <i class="fas fa-user mr-2"></i>{{ $user->name }}
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
        
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">
                Hello, {{ $user->name }}! 👋
            </h2>
            <p class="text-gray-600 mt-2">Welcome to your workspace</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">My Ideas</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['my_ideas'] }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-lightbulb text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Approved</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['approved_ideas'] }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_ideas'] }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Team Size</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['total_team'] }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-users text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Ideas -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-list text-indigo-600 mr-2"></i>My Ideas
                </h3>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-plus mr-2"></i>Submit New Idea
                </button>
            </div>
            <div class="p-6">
                @forelse($myIdeas as $idea)
                    <div class="py-4 border-b border-gray-100 last:border-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $idea->title }}</h4>
                                <p class="text-sm text-gray-500 mt-1">{{ $idea->description }}</p>
                                <p class="text-xs text-gray-400 mt-2">
                                    Submitted {{ $idea->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                {{ $idea->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $idea->status === 'new' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $idea->status === 'pending_pricing' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            ">
                                {{ ucfirst(str_replace('_', ' ', $idea->status)) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <i class="fas fa-lightbulb text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500">You haven't submitted any ideas yet</p>
                        <button class="mt-4 px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Submit Your First Idea
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Team Ideas -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-users text-purple-600 mr-2"></i>Recent Team Ideas
                </h3>
            </div>
            <div class="p-6">
                @forelse($allIdeas as $idea)
                    <div class="py-3 border-b border-gray-100 last:border-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $idea->title }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    by {{ $idea->creator->name }} • {{ $idea->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $idea->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $idea->status === 'new' ? 'bg-blue-100 text-blue-800' : '' }}
                            ">
                                {{ ucfirst(str_replace('_', ' ', $idea->status)) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No team ideas yet</p>
                @endforelse
            </div>
        </div>

    </div>

</body>
</html>
