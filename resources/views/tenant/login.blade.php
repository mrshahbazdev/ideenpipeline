<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ $tenant->admin_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 to-purple-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-full mb-4">
                    <span class="text-3xl text-white">🏢</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    {{ $tenant->admin_name }}
                </h1>
                <p class="text-gray-600">
                    Sign in to your account
                </p>
            </div>

            <!-- Login Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                @if($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                        <div class="flex items-start">
                            <span class="text-red-500 text-xl mr-3">⚠️</span>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-red-800">
                                    {{ $errors->first() }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                        <div class="flex items-start">
                            <span class="text-green-500 text-xl mr-3">✅</span>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-green-800">
                                    {{ session('success') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('tenant.login.post', $tenantId) }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email') }}"
                            required 
                            autofocus
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="you@example.com"
                        >
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="Enter your password"
                        >
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            id="remember"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition transform hover:scale-[1.02] active:scale-[0.98]"
                    >
                        Sign In
                    </button>
                </form>

                <!-- Footer Links -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Need help? 
                        <a href="https://ideenpipeline.de" target="_blank" class="text-indigo-600 hover:text-indigo-700 font-medium">
                            Contact Support
                        </a>
                    </p>
                </div>
            </div>

            <!-- Tenant Info -->
            <div class="mt-8 text-center">
                <div class="inline-flex items-center space-x-2 text-sm text-gray-500">
                    <span>🔒</span>
                    <span>Secure tenant environment</span>
                </div>
                <p class="text-xs text-gray-400 mt-2 font-mono">
                    {{ $tenant->subdomain }}.ideenpipeline.de
                </p>
            </div>

            <!-- Subscription Info -->
            @if($tenant->expires_at)
                @php
                    $daysRemaining = $tenant->daysRemaining();
                @endphp
                
                @if($daysRemaining > 0 && $daysRemaining <= 7)
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm text-yellow-800 text-center">
                            ⚠️ Your subscription expires in <strong>{{ $daysRemaining }} days</strong>
                        </p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Background Pattern -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-1/2 -right-1/2 w-full h-full bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full opacity-20 blur-3xl"></div>
        <div class="absolute -bottom-1/2 -left-1/2 w-full h-full bg-gradient-to-tr from-blue-100 to-cyan-100 rounded-full opacity-20 blur-3xl"></div>
    </div>
</body>
</html>
