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
    @include('tenant.partials.nav')

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">
                Hello, {{ $user->name }}! 👋
            </h2>
            <p class="text-gray-600 mt-2">Welcome to your workspace at {{ $tenant->subdomain }}</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Team Members</p>
                        <p class="text-4xl font-bold mt-2">{{ $stats['total_team'] }}</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-full">
                        <i class="fas fa-users text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Developers</p>
                        <p class="text-4xl font-bold mt-2">{{ $stats['developers'] }}</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-full">
                        <i class="fas fa-code text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Work-Bees</p>
                        <p class="text-4xl font-bold mt-2">{{ $stats['work_bees'] }}</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-full">
                        <i class="fas fa-user-friends text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Welcome Card -->
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="inline-block p-4 bg-indigo-100 rounded-full mb-4">
                <i class="fas fa-rocket text-indigo-600 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">
                Welcome to {{ $tenant->subdomain }}!
            </h3>
            <p class="text-gray-600 mb-6">
                You're now part of the team. Start collaborating with {{ $stats['total_team'] - 1 }} other members.
            </p>
            <div class="flex justify-center gap-4">
                <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-plus mr-2"></i>Create New Idea
                </button>
                <button class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-list mr-2"></i>View Ideas
                </button>
            </div>
        </div>

        <!-- Team Members (if any) -->
        @if($teamMembers->count() > 0)
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-users text-indigo-600 mr-2"></i>Your Team
            </h3>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($teamMembers as $member)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-white
                            {{ $member->role === 'developer' ? 'bg-purple-500' : 'bg-green-500' }}
                        ">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">{{ $member->name }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst(str_replace('-', ' ', $member->role)) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

</body>
</html>
