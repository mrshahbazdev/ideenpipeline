<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ $tenant->admin_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl mr-2">🏢</span>
                    <h1 class="text-xl font-bold text-indigo-600">
                        {{ $tenant->admin_name }}
                    </h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">{{ $user->name ?? 'User' }}</span>
                    <form method="POST" action="{{ route('tenant.logout', $tenant->id) }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Expiry Warning -->
    @php
        $daysRemaining = $tenant->daysRemaining();
    @endphp
    
    @if($daysRemaining > 0 && $daysRemaining <= 7)
        <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4">
            <div class="max-w-7xl mx-auto">
                <p class="text-yellow-700">
                    ⚠️ Your subscription expires in <strong>{{ $daysRemaining }} days</strong>.
                    <a href="http://127.0.0.1:8000" class="underline font-semibold">Renew Now</a>
                </p>
            </div>
        </div>
    @elseif($tenant->isExpired())
        <div class="bg-red-100 border-l-4 border-red-500 p-4">
            <div class="max-w-7xl mx-auto">
                <p class="text-red-700">
                    ❌ Your subscription has expired. 
                    <a href="http://127.0.0.1:8000" class="underline font-semibold">Renew Now</a>
                </p>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg shadow-lg p-8 mb-6">
            <h2 class="text-3xl font-bold mb-2">
                🎉 Welcome to Your Dashboard!
            </h2>
            <p class="text-indigo-100">
                You're successfully logged into your isolated tenant environment.
            </p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Subscription -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Subscription</h3>
                    <span class="text-3xl">📦</span>
                </div>
                <p class="text-2xl font-bold text-indigo-600">{{ $tenant->package_name }}</p>
                @if($tenant->expires_at)
                    <p class="text-sm text-gray-500 mt-2">
                        Expires: {{ $tenant->expires_at->format('M d, Y') }}
                    </p>
                @else
                    <p class="text-sm text-green-600 mt-2 font-semibold">♾️ Lifetime Access</p>
                @endif
            </div>

            <!-- Days Remaining -->
            @if($tenant->expires_at && $daysRemaining > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Days Left</h3>
                    <span class="text-3xl">📅</span>
                </div>
                <p class="text-2xl font-bold {{ $daysRemaining <= 7 ? 'text-orange-600' : 'text-green-600' }}">
                    {{ $daysRemaining }}
                </p>
                <p class="text-sm text-gray-500 mt-2">Until renewal required</p>
            </div>
            @endif

            <!-- Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Status</h3>
                    <span class="text-3xl">{{ $tenant->isActive() ? '✅' : '❌' }}</span>
                </div>
                <p class="text-2xl font-bold {{ $tenant->isActive() ? 'text-green-600' : 'text-red-600' }} capitalize">
                    {{ $tenant->status }}
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    {{ $tenant->isActive() ? 'All systems operational' : 'Subscription inactive' }}
                </p>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-xl font-bold mb-6">🚀 Your Features</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start space-x-3 p-4 bg-blue-50 rounded-lg">
                    <span class="text-2xl">🔒</span>
                    <div>
                        <h4 class="font-semibold text-gray-900">Isolated Database</h4>
                        <p class="text-sm text-gray-600">Your data is completely separate and secure</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3 p-4 bg-green-50 rounded-lg">
                    <span class="text-2xl">👤</span>
                    <div>
                        <h4 class="font-semibold text-gray-900">Admin Access</h4>
                        <p class="text-sm text-gray-600">Full control over your workspace</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3 p-4 bg-purple-50 rounded-lg">
                    <span class="text-2xl">🌐</span>
                    <div>
                        <h4 class="font-semibold text-gray-900">Unique Domain</h4>
                        <p class="text-sm text-gray-600 font-mono">{{ $tenant->domain ?? $tenant->subdomain . '.local:8001' }}</p>

                    </div>
                </div>
                <div class="flex items-start space-x-3 p-4 bg-yellow-50 rounded-lg">
                    <span class="text-2xl">🛡️</span>
                    <div>
                        <h4 class="font-semibold text-gray-900">Secure Environment</h4>
                        <p class="text-sm text-gray-600">Protected by authentication</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical Details -->
        <div class="bg-gray-800 text-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <span class="mr-2">🔧</span>
                Technical Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm font-mono">
                <div>
                    <p class="text-gray-400 mb-1">Tenant ID:</p>
                    <p class="text-green-400">{{ $tenant->id }}</p>
                </div>
                <div>
                    <p class="text-gray-400 mb-1">Database:</p>
                    <p class="text-green-400">{{ $tenant->tenancy_db_name }}</p>
                </div>
                <div>
                    <p class="text-gray-400 mb-1">Admin Email:</p>
                    <p class="text-green-400">{{ $tenant->admin_email }}</p>
                </div>
                <div>
                    <p class="text-gray-400 mb-1">Created:</p>
                    <p class="text-green-400">{{ $tenant->created_at->format('M d, Y H:i') }}</p>
                </div>
                @if($tenant->starts_at)
                <div>
                    <p class="text-gray-400 mb-1">Started:</p>
                    <p class="text-green-400">{{ $tenant->starts_at->format('M d, Y') }}</p>
                </div>
                @endif
                @if($tenant->expires_at)
                <div>
                    <p class="text-gray-400 mb-1">Expires:</p>
                    <p class="text-green-400">{{ $tenant->expires_at->format('M d, Y') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Links -->
        <div class="mt-6 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4">🔗 Quick Links</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="http://127.0.0.1:8000" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <span class="text-2xl mr-3">🏠</span>
                    <div>
                        <p class="font-semibold">Main Platform</p>
                        <p class="text-sm text-gray-600">Manage subscriptions</p>
                    </div>
                </a>
                <a href="/" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <span class="text-2xl mr-3">🏢</span>
                    <div>
                        <p class="font-semibold">All Tenants</p>
                        <p class="text-sm text-gray-600">View tenant list</p>
                    </div>
                </a>
                <a href="http://127.0.0.1:8001/api/health" target="_blank" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <span class="text-2xl mr-3">💚</span>
                    <div>
                        <p class="font-semibold">Server Health</p>
                        <p class="text-sm text-gray-600">Check API status</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
