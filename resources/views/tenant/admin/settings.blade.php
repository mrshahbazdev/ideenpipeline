<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50" x-data="{ mobileMenuOpen: false }">

    @include('tenant.partials.nav')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-cog text-indigo-600 mr-3"></i>
                Tenant Settings
            </h1>
            <p class="text-gray-600 mt-2">Configure your organization settings</p>

            <!-- Breadcrumb -->
            <div class="flex items-center space-x-2 text-sm text-gray-600 mt-4">
                <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" class="hover:text-indigo-600">Dashboard</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900 font-medium">Settings</span>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm animate-pulse">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('tenant.admin.settings.update', ['tenantId' => $tenant->id]) }}">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-building text-indigo-600 mr-2"></i>
                    Organization Information
                </h2>

                <div class="space-y-6">
                    <!-- Subdomain -->
                    <div>
                        <label for="subdomain" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-link text-gray-400 mr-2"></i>Subdomain *
                        </label>
                        <div class="flex items-center">
                            <input 
                                type="text" 
                                name="subdomain" 
                                id="subdomain"
                                value="{{ old('subdomain', $tenant->subdomain) }}"
                                required
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('subdomain') border-red-500 @enderror"
                                placeholder="your-company"
                            >
                            <span class="px-4 py-3 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-gray-600">
                                .ideenpipeline.de
                            </span>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Your organization's unique identifier. Changing this will affect your URL.
                        </p>
                        @error('subdomain')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Tenant ID (Read-only) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-fingerprint text-gray-400 mr-2"></i>Tenant ID
                        </label>
                        <input 
                            type="text" 
                            value="{{ $tenant->id }}"
                            disabled
                            class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed font-mono text-sm"
                        >
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-lock mr-1"></i>This is your unique tenant identifier and cannot be changed.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Status & Subscription -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-toggle-on text-indigo-600 mr-2"></i>
                    Status & Subscription
                </h2>

                <div class="space-y-6">
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-circle text-gray-400 mr-2"></i>Account Status *
                        </label>
                        <select 
                            name="status" 
                            id="status"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('status') border-red-500 @enderror"
                        >
                            <option value="active" {{ old('status', $tenant->status) === 'active' ? 'selected' : '' }}>
                                ✅ Active - Fully operational
                            </option>
                            <option value="suspended" {{ old('status', $tenant->status) === 'suspended' ? 'selected' : '' }}>
                                ⏸️ Suspended - Temporarily disabled
                            </option>
                            <option value="expired" {{ old('status', $tenant->status) === 'expired' ? 'selected' : '' }}>
                                ⏰ Expired - Subscription ended
                            </option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Current Status Display -->
                    <div class="p-4 rounded-lg {{ 
                        $tenant->status === 'active' ? 'bg-green-50 border border-green-200' : 
                        ($tenant->status === 'suspended' ? 'bg-yellow-50 border border-yellow-200' : 'bg-red-50 border border-red-200') 
                    }}">
                        <div class="flex items-center">
                            <i class="fas fa-circle text-sm mr-2 {{ 
                                $tenant->status === 'active' ? 'text-green-500' : 
                                ($tenant->status === 'suspended' ? 'text-yellow-500' : 'text-red-500') 
                            }}"></i>
                            <p class="text-sm font-semibold {{ 
                                $tenant->status === 'active' ? 'text-green-800' : 
                                ($tenant->status === 'suspended' ? 'text-yellow-800' : 'text-red-800') 
                            }}">
                                Current Status: {{ ucfirst($tenant->status) }}
                            </p>
                        </div>
                    </div>

                    <!-- Expiration Date -->
                    <div>
                        <label for="expires_at" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar text-gray-400 mr-2"></i>Subscription Expires
                        </label>
                        <input 
                            type="date" 
                            name="expires_at" 
                            id="expires_at"
                            value="{{ old('expires_at', $tenant->expires_at ? $tenant->expires_at->format('Y-m-d') : '') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('expires_at') border-red-500 @enderror"
                        >
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Leave empty for no expiration date.
                        </p>
                        @error('expires_at')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-lg p-8 mb-6 border border-blue-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Account Information
                </h2>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Created</p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $tenant->created_at->format('F d, Y') }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $tenant->created_at->diffForHumans() }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-600 mb-1">Last Updated</p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $tenant->updated_at->format('F d, Y') }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $tenant->updated_at->diffForHumans() }}</p>
                    </div>

                    @if($tenant->expires_at)
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Expires</p>
                            <p class="text-sm font-semibold {{ $tenant->expires_at->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $tenant->expires_at->format('F d, Y') }}
                            </p>
                            <p class="text-xs {{ $tenant->expires_at->isPast() ? 'text-red-500' : 'text-gray-500' }}">
                                {{ $tenant->expires_at->isPast() ? 'Expired ' : '' }}{{ $tenant->expires_at->diffForHumans() }}
                            </p>
                        </div>
                    @endif

                    <div>
                        <p class="text-xs text-gray-600 mb-1">Access URL</p>
                        <a href="https://{{ $tenant->subdomain }}.ideenpipeline.de" 
                           target="_blank"
                           class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 break-all">
                            {{ $tenant->subdomain }}.ideenpipeline.de
                            <i class="fas fa-external-link-alt text-xs ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Warning -->
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-lg mb-6">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3 mt-1"></i>
                    <div>
                        <p class="text-sm font-semibold text-yellow-900 mb-1">Important Notice</p>
                        <p class="text-xs text-yellow-800">
                            Changing the subdomain or status will affect all users. Make sure you understand the implications before saving changes.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between">
                <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" 
                   class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition font-semibold">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button 
                    type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-semibold hover:from-indigo-700 hover:to-purple-700 transition shadow-lg transform hover:scale-105">
                    <i class="fas fa-save mr-2"></i>Save Settings
                </button>
            </div>

        </form>

    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>
