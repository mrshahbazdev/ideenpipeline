<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - {{ tenant()->admin_name ?? 'CRM Tool' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-900">
                        {{ tenant()->admin_name ?? 'CRM Tool' }}
                    </h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('tenant.logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Expiry Warning -->
    @if(tenant()->daysRemaining() > 0 && tenant()->daysRemaining() <= 7)
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <p class="font-bold">⚠️ Subscription Expiring Soon</p>
                <p>Your subscription will expire in {{ tenant()->daysRemaining() }} day(s). 
                   <a href="{{ route('tenant.upgrade') }}" class="underline font-semibold">Upgrade Now</a>
                </p>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>
</body>
</html>
