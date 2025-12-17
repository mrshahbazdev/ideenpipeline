<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tenant->admin_name }} - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full space-y-8">
            <!-- Tenant Info -->
            <div class="text-center">
                <div class="mb-4 inline-block p-4 bg-indigo-100 rounded-full">
                    <span class="text-4xl">🏢</span>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">
                    {{ $tenant->admin_name }}
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Tenant Login - {{ $tenant->package_name }}
                </p>
                @if($tenant->expires_at)
                    <p class="text-xs text-gray-500 mt-1">
                        Valid until: {{ $tenant->expires_at->format('M d, Y') }}
                    </p>
                @endif
            </div>

            <!-- Login Form -->
            <div class="bg-white py-8 px-6 shadow-lg rounded-lg">
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4">
                        <p class="text-red-700 text-sm">{{ $errors->first() }}</p>
                    </div>
                @endif

                @if(session('success'))
                    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4">
                        <p class="text-green-700 text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('tenant.login.post', $tenant->id) }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input id="email" name="email" type="email" required autofocus
                               value="{{ old('email') }}"
                               placeholder="admin@example.com"
                               class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input id="password" name="password" type="password" required
                               placeholder="••••••••"
                               class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        Sign in →
                    </button>
                </form>

                <!-- Test Credentials Hint (Remove in production) -->
                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                    <p class="text-xs text-blue-700 font-semibold mb-1">Test Credentials:</p>
                    <p class="text-xs text-blue-600">Email: {{ $tenant->admin_email }}</p>
                    <p class="text-xs text-blue-600">Password: (from subscription)</p>
                </div>
            </div>

            <!-- Back Links -->
            <div class="text-center space-y-2">
                <a href="/" class="block text-sm text-gray-600 hover:text-indigo-600">
                    ← View All Tenants
                </a>
                <a href="http://127.0.0.1:8000" class="block text-sm text-indigo-600 hover:text-indigo-800">
                    Back to Main Platform
                </a>
            </div>
        </div>
    </div>
</body>
</html>
