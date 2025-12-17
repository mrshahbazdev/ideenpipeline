<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New User - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50" x-data="{ mobileMenuOpen: false, showPassword: false, selectedRole: 'standard' }">

    @include('tenant.partials.nav')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <a href="{{ route('tenant.admin.users.index', ['tenantId' => $tenant->id]) }}" 
                   class="text-gray-600 hover:text-gray-900 transition">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-user-plus text-red-600 mr-3"></i>
                        Create New User
                    </h1>
                    <p class="text-gray-600 mt-2">Add a new user to your organization</p>
                </div>
            </div>

            <!-- Breadcrumb -->
            <div class="flex items-center space-x-2 text-sm text-gray-600 ml-14">
                <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" class="hover:text-indigo-600">Dashboard</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <a href="{{ route('tenant.admin.users.index', ['tenantId' => $tenant->id]) }}" class="hover:text-indigo-600">Users</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900 font-medium">Create</span>
            </div>
        </div>

        <form method="POST" action="{{ route('tenant.admin.users.store', ['tenantId' => $tenant->id]) }}">
            @csrf

            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-id-card text-indigo-600 mr-2"></i>
                    Basic Information
                </h2>

                <div class="space-y-6">
                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-gray-400 mr-2"></i>Full Name *
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition @error('name') border-red-500 @enderror"
                            placeholder="Enter full name"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope text-gray-400 mr-2"></i>Email Address *
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition @error('email') border-red-500 @enderror"
                            placeholder="user@example.com"
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock text-gray-400 mr-2"></i>Password *
                        </label>
                        <div class="relative">
                            <input 
                                :type="showPassword ? 'text' : 'password'" 
                                name="password" 
                                id="password"
                                required
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition @error('password') border-red-500 @enderror"
                                placeholder="Enter secure password"
                            >
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Minimum 8 characters, include letters, numbers, and symbols
                        </p>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Role & Permissions -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-user-shield text-indigo-600 mr-2"></i>
                    Role & Permissions
                </h2>

                <div class="space-y-4">
                    <!-- Role Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Select Role *
                        </label>
                        <div class="grid md:grid-cols-2 gap-4">
                            <!-- Admin -->
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="admin" x-model="selectedRole" 
                                       {{ old('role') === 'admin' ? 'checked' : '' }}
                                       class="peer sr-only">
                                <div class="p-6 border-2 border-gray-200 rounded-xl peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-300 transition">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-lg flex items-center justify-center shadow-lg">
                                            <i class="fas fa-crown text-white text-xl"></i>
                                        </div>
                                        <span class="hidden peer-checked:inline-flex w-6 h-6 bg-red-500 text-white rounded-full items-center justify-center">
                                            <i class="fas fa-check text-xs"></i>
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-gray-900 mb-2">Admin</h3>
                                    <p class="text-xs text-gray-600 leading-relaxed">
                                        Full access to all features, manage users, teams, and system settings
                                    </p>
                                </div>
                            </label>

                            <!-- Developer -->
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="developer" x-model="selectedRole"
                                       {{ old('role') === 'developer' ? 'checked' : '' }}
                                       class="peer sr-only">
                                <div class="p-6 border-2 border-gray-200 rounded-xl peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-purple-300 transition">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg">
                                            <i class="fas fa-code text-white text-xl"></i>
                                        </div>
                                        <span class="hidden peer-checked:inline-flex w-6 h-6 bg-purple-500 text-white rounded-full items-center justify-center">
                                            <i class="fas fa-check text-xs"></i>
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-gray-900 mb-2">Developer</h3>
                                    <p class="text-xs text-gray-600 leading-relaxed">
                                        Can edit solution, duration, and cost fields in ideas
                                    </p>
                                </div>
                            </label>

                            <!-- Work-Bee -->
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="work-bee" x-model="selectedRole"
                                       {{ old('role') === 'work-bee' ? 'checked' : '' }}
                                       class="peer sr-only">
                                <div class="p-6 border-2 border-gray-200 rounded-xl peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300 transition">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-lg">
                                            <i class="fas fa-user-friends text-white text-xl"></i>
                                        </div>
                                        <span class="hidden peer-checked:inline-flex w-6 h-6 bg-green-500 text-white rounded-full items-center justify-center">
                                            <i class="fas fa-check text-xs"></i>
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-gray-900 mb-2">Work-Bee</h3>
                                    <p class="text-xs text-gray-600 leading-relaxed">
                                        Can edit pain score and implementation status in ideas
                                    </p>
                                </div>
                            </label>

                            <!-- Standard -->
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="standard" x-model="selectedRole"
                                       {{ old('role', 'standard') === 'standard' ? 'checked' : '' }}
                                       class="peer sr-only">
                                <div class="p-6 border-2 border-gray-200 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300 transition">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center shadow-lg">
                                            <i class="fas fa-user text-white text-xl"></i>
                                        </div>
                                        <span class="hidden peer-checked:inline-flex w-6 h-6 bg-blue-500 text-white rounded-full items-center justify-center">
                                            <i class="fas fa-check text-xs"></i>
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-gray-900 mb-2">Standard</h3>
                                    <p class="text-xs text-gray-600 leading-relaxed">
                                        Can submit ideas, vote, and participate in team discussions
                                    </p>
                                </div>
                            </label>
                        </div>
                        @error('role')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Permissions Preview -->
                    <div class="mt-6 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-200">
                        <p class="text-sm font-semibold text-indigo-900 mb-3">
                            <i class="fas fa-info-circle mr-2"></i>
                            Selected Role Permissions:
                        </p>
                        <ul class="space-y-2 text-sm text-indigo-800">
                            <template x-if="selectedRole === 'admin'">
                                <div>
                                    <li><i class="fas fa-check text-green-600 mr-2"></i>Manage all users and teams</li>
                                    <li><i class="fas fa-check text-green-600 mr-2"></i>Full idea editing capabilities</li>
                                    <li><i class="fas fa-check text-green-600 mr-2"></i>Access to analytics and settings</li>
                                    <li><i class="fas fa-check text-green-600 mr-2"></i>Approve/reject ideas</li>
                                </div>
                            </template>
                            <template x-if="selectedRole === 'developer'">
                                <div>
                                    <li><i class="fas fa-check text-green-600 mr-2"></i>Edit solution, duration, and cost</li>
                                    <li><i class="fas fa-check text-green-600 mr-2"></i>Submit and vote on ideas</li>
                                    <li><i class="fas fa-check text-green-600 mr-2"></i>Join teams and participate</li>
                                </div>
                            </template>
                            <template x-if="selectedRole === 'work-bee'">
                                <div>
                                    <li><i class="fas fa-check text-green-600 mr-2"></i>Edit pain score and implementation status</li>
                                    <li><i class="fas fa-check text-green-600 mr-2"></i>Submit and vote on ideas</li>
                                    <li><i class="fas fa-check text-green-600 mr-2"></i>Join teams and participate</li>
                                </div>
                            </template>
                            <template x-if="selectedRole === 'standard'">
                                <div>
                                    <li><i class="fas fa-check text-green-600 mr-2"></i>Submit new ideas</li>
                                    <li><i class="fas fa-check text-green-600 mr-2"></i>Vote and comment on ideas</li>
                                    <li><i class="fas fa-check text-green-600 mr-2"></i>Join and participate in teams</li>
                                </div>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Account Status -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-toggle-on text-indigo-600 mr-2"></i>
                    Account Status
                </h2>

                <div class="flex items-center space-x-3">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        id="is_active"
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-5 h-5 text-green-600 rounded focus:ring-2 focus:ring-green-500"
                    >
                    <label for="is_active" class="text-sm font-medium text-gray-700">
                        Activate user account immediately
                    </label>
                </div>
                <p class="mt-2 text-xs text-gray-500 ml-8">
                    <i class="fas fa-info-circle mr-1"></i>
                    If unchecked, user will not be able to login until activated
                </p>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between">
                <a href="{{ route('tenant.admin.users.index', ['tenantId' => $tenant->id]) }}" 
                   class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition font-semibold">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button 
                    type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-lg font-semibold hover:from-red-700 hover:to-pink-700 transition shadow-lg transform hover:scale-105">
                    <i class="fas fa-user-plus mr-2"></i>Create User
                </button>
            </div>

        </form>

    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>
