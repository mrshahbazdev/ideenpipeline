<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Tool - Tenants</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <nav class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="text-3xl">🏢</span>
                        <h1 class="text-2xl font-bold">CRM Tool</h1>
                    </div>
                    <a href="http://127.0.0.1:8000" target="_blank" class="bg-white text-indigo-600 px-4 py-2 rounded-lg font-semibold hover:bg-indigo-50 transition">
                        🏠 Main Platform
                    </a>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 py-12">
            <!-- Welcome Message -->
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Welcome to CRM Tool
                </h2>
                <p class="text-xl text-gray-600">
                    Single Database Multi-Tenancy Platform
                </p>
            </div>

            <!-- Stats -->
            @php
                $totalTenants = \App\Models\Tenant::count();
                $activeTenants = \App\Models\Tenant::where('status', 'active')->count();
                $totalUsers = \App\Models\User::withoutGlobalScope('tenant')->count();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                    <div class="text-4xl mb-2">📊</div>
                    <div class="text-3xl font-bold text-indigo-600">{{ $totalTenants }}</div>
                    <div class="text-gray-600 mt-1">Total Tenants</div>
                </div>
                <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                    <div class="text-4xl mb-2">✅</div>
                    <div class="text-3xl font-bold text-green-600">{{ $activeTenants }}</div>
                    <div class="text-gray-600 mt-1">Active Tenants</div>
                </div>
                <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                    <div class="text-4xl mb-2">👥</div>
                    <div class="text-3xl font-bold text-purple-600">{{ $totalUsers }}</div>
                    <div class="text-gray-600 mt-1">Total Users</div>
                </div>
            </div>

            <!-- Available Tenants -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6">📋 Active Tenants</h2>

                @php
                    $tenants = \App\Models\Tenant::where('status', 'active')->get();
                @endphp

                @if($tenants->isEmpty())
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">🏢</div>
                        <p class="text-gray-500 text-lg mb-2">No active tenants found.</p>
                        <p class="text-sm text-gray-400">Create a subscription from the main platform.</p>
                        <a href="http://127.0.0.1:8000" target="_blank" class="inline-block mt-4 bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                            Go to Main Platform
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($tenants as $tenant)
                            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">
                                            {{ $tenant->admin_name }}
                                        </h3>
                                        <p class="text-sm text-gray-500">{{ $tenant->admin_email }}</p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $tenant->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $tenant->isActive() ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <span class="mr-2">📦</span>
                                        <span>{{ $tenant->package_name }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <span class="mr-2">🌐</span>
                                        <span class="font-mono text-xs">{{ $tenant->subdomain }}</span>
                                    </div>
                                    @if($tenant->expires_at)
                                        <div class="flex items-center text-sm text-gray-600">
                                            <span class="mr-2">📅</span>
                                            <span>Expires: {{ $tenant->expires_at->format('M d, Y') }}</span>
                                        </div>
                                    @endif
                                </div>

                                @if($tenant->isActive())
                                    <a href="{{ url('/tenant/' . $tenant->id . '/login') }}" 
                                       class="block w-full bg-indigo-600 text-white text-center py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                                        🚀 Access Tenant
                                    </a>
                                @else
                                    <div class="block w-full bg-gray-300 text-gray-600 text-center py-2 rounded-lg cursor-not-allowed">
                                        ⛔ Inactive
                                    </div>
                                @endif

                                <!-- Technical Details -->
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <details class="text-xs text-gray-600">
                                        <summary class="cursor-pointer font-semibold hover:text-indigo-600">
                                            Technical Details
                                        </summary>
                                        <div class="mt-2 space-y-1 font-mono text-xs bg-gray-50 p-2 rounded">
                                            <div>ID: {{ $tenant->id }}</div>
                                            <div>Created: {{ $tenant->created_at->format('Y-m-d H:i') }}</div>
                                            @php
                                                $userCount = \App\Models\User::withoutGlobalScope('tenant')
                                                    ->where('tenant_id', $tenant->id)
                                                    ->count();
                                            @endphp
                                            <div>Users: {{ $userCount }}</div>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Architecture Info -->
            <div class="mt-12 bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
                <h3 class="text-xl font-bold text-blue-900 mb-3">
                    📊 Single Database Multi-Tenancy Architecture
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-blue-800">
                    <div>
                        <h4 class="font-semibold mb-2">✨ Benefits:</h4>
                        <ul class="space-y-1 ml-4">
                            <li>• Single database - easy management</li>
                            <li>• Automatic tenant isolation via scopes</li>
                            <li>• Cost-effective hosting solution</li>
                            <li>• Fast cross-tenant reporting</li>
                            <li>• Simple backup and restore</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-2">🔒 Security:</h4>
                        <ul class="space-y-1 ml-4">
                            <li>• Automatic query scoping by tenant_id</li>
                            <li>• Session-based tenant identification</li>
                            <li>• Isolated authentication per tenant</li>
                            <li>• Protected routes with middleware</li>
                            <li>• Data segregation at model level</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- API Info -->
            <div class="mt-6 bg-gray-800 text-white rounded-lg p-6">
                <h3 class="text-xl font-bold mb-4">🔧 API Endpoints</h3>
                <div class="space-y-2 text-sm font-mono">
                    <div class="flex items-center justify-between py-2 border-b border-gray-700">
                        <span class="text-green-400">GET</span>
                        <span class="flex-1 ml-4">/api/health</span>
                        <span class="text-gray-400">Health check</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-700">
                        <span class="text-yellow-400">POST</span>
                        <span class="flex-1 ml-4">/api/tenants/create</span>
                        <span class="text-gray-400">Create tenant</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-700">
                        <span class="text-yellow-400">POST</span>
                        <span class="flex-1 ml-4">/api/tenants/{id}/update-password</span>
                        <span class="text-gray-400">Sync password</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-blue-400">GET</span>
                        <span class="flex-1 ml-4">/api/tenants/{id}/status</span>
                        <span class="text-gray-400">Check status</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-700">
                    <a href="http://127.0.0.1:8001/api/health" target="_blank" class="text-green-400 hover:text-green-300 text-sm">
                        Test Health Endpoint →
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-6 mt-12">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-sm text-gray-400">
                    CRM Tool - Single Database Multi-Tenancy Platform
                </p>
                <p class="text-xs text-gray-500 mt-2">
                    Powered by Laravel {{ app()->version() }}
                </p>
            </div>
        </footer>
    </div>
</body>
</html>
