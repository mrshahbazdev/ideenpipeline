<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {{ $tenant->subdomain }} | Innovation Pipeline</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="gradient-bg inline-block p-4 rounded-full mb-4">
                    <i class="fas fa-user-plus text-white text-3xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">Join Our Team</h2>
                <p class="mt-2 text-gray-600">
                    Create your account in {{ $tenant->subdomain }}
                </p>
            </div>

            <!-- Tenant Info -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-3">
                    <i class="fas fa-building text-indigo-600 text-xl"></i>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $tenant->admin_name }}</p>
                        <p class="text-sm text-gray-600">{{ $tenant->subdomain }}.ideenpipeline.de</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white py-8 px-6 shadow-xl rounded-lg">
                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('tenant.register.post', ['tenantId' => $tenant->id]) }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-gray-400 mr-2"></i>Full Name *
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('name') border-red-500 @enderror"
                            placeholder="Enter your full name"
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">
                                <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope text-gray-400 mr-2"></i>Email Address *
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('email') border-red-500 @enderror"
                            placeholder="your.email@example.com"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">
                                <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-gray-400 mr-2"></i>Password *
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('password') border-red-500 @enderror"
                            placeholder="Minimum 8 characters"
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">
                                <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-gray-400 mr-2"></i>Confirm Password *
                        </label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="Re-enter your password"
                        >
                    </div>

                    <!-- Role Info -->
                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            You'll be registered as a <strong>Standard User</strong>. 
                            The admin can assign you roles like <strong>Work-Bee</strong> or <strong>Developer</strong> later based on your responsibilities.
                        </p>
                    </div>


                    <!-- Submit -->
                    <button 
                        type="submit"
                        class="w-full gradient-bg text-white py-3 rounded-lg font-semibold hover:opacity-90 transition transform hover:scale-[1.02]"
                    >
                        <i class="fas fa-user-plus mr-2"></i>Create Account
                    </button>
                </form>

                <!-- Login Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('tenant.login', ['tenantId' => $tenant->id]) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                            <i class="fas fa-sign-in-alt mr-1"></i>Login here
                        </a>
                    </p>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="mt-6 text-center">
                <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-gray-900 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>

</body>
</html>
