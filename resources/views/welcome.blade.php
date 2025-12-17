<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Innovation Pipeline Management</title>
    <meta name="description" content="Centralize, manage, and execute all your innovative project ideas with our powerful Innovation Pipeline platform.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        * { font-family: 'Inter', sans-serif; }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            margin-bottom: 20px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s;
            display: inline-block;
            text-decoration: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .tenant-subdomain {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border: 2px solid #667eea;
            padding: 4px 12px;
            border-radius: 6px;
            font-weight: 600;
            color: #667eea;
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold gradient-text">Innovation Pipeline</h1>
                    
                    @if($tenant)
                        <span class="ml-4 tenant-subdomain">
                            {{ $tenant->subdomain }}
                        </span>
                    @endif
                </div>
                
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant?->id ?? 'default']) }}" class="text-gray-700 hover:text-indigo-600 font-medium">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <form method="POST" action="{{ route('tenant.logout', ['tenantId' => $tenant?->id ?? 'default']) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-red-600 font-medium">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    @else
                        @if($tenant)
                            <a href="{{ route('tenant.register', ['tenantId' => $tenant->id]) }}" class="text-gray-700 hover:text-indigo-600 font-medium">
                                <i class="fas fa-user-plus mr-2"></i>Register
                            </a>
                            <a href="{{ route('tenant.login', ['tenantId' => $tenant->id]) }}" class="text-gray-700 hover:text-indigo-600 font-medium">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </a>
                        @else
                            <a href="https://cip-tools.de" class="text-gray-700 hover:text-indigo-600 font-medium">
                                <i class="fas fa-globe mr-2"></i>Main Platform
                            </a>
                        @endif
                    @endauth
                </div>

            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    @if($tenant)
                        <div class="inline-block bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-semibold mb-6">
                            <i class="fas fa-building mr-2"></i>{{ $tenant->admin_name }}'s Innovation Hub
                        </div>
                    @else
                        <div class="inline-block bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-semibold mb-6">
                            <i class="fas fa-rocket mr-2"></i>Multi-Tenant SaaS Platform
                        </div>
                    @endif
                    
                    <h1 class="text-5xl md:text-6xl font-bold leading-tight mb-6">
                        Transform Ideas Into Reality
                    </h1>
                    <p class="text-xl text-white/90 mb-8">
                        Centralize, manage, and execute all your innovative project ideas with our powerful Innovation Pipeline platform.
                    </p>
                    
                    <div class="flex flex-wrap gap-4">
                        @auth
                            <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant?->id ?? 'default']) }}" class="btn-primary">
                                <i class="fas fa-rocket mr-2"></i>Go to Dashboard
                            </a>
                        @else
                            @if($tenant)
                                <a href="{{ route('tenant.login', ['tenantId' => $tenant->id]) }}" class="btn-primary">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Login to {{ $tenant->subdomain }}
                                </a>
                            @else
                                <a href="https://cip-tools.de/login" class="btn-primary">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Get Started
                                </a>
                            @endif
                        @endauth
                    </div>
                    
                    @if($tenant)
                        <div class="mt-8 p-4 bg-white/10 backdrop-blur-sm rounded-lg">
                            <p class="text-sm text-white/80 mb-2">
                                <i class="fas fa-info-circle mr-2"></i>Tenant Information
                            </p>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-white/70">Package:</span>
                                    <span class="ml-2 font-semibold">{{ $tenant->package_name }}</span>
                                </div>
                                <div>
                                    <span class="text-white/70">Status:</span>
                                    <span class="ml-2 font-semibold">
                                        @if($tenant->isActive())
                                            <i class="fas fa-check-circle text-green-400"></i> Active
                                        @else
                                            <i class="fas fa-exclamation-circle text-yellow-400"></i> Expired
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="hidden md:block">
                    <img src="https://illustrations.popsy.co/amber/innovation-idea.svg" alt="Innovation" class="w-full h-auto">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                @if($tenant)
                    <div>
                        <div class="text-4xl font-bold gradient-text mb-2">100+</div>
                        <div class="text-gray-600">Ideas Managed</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold gradient-text mb-2">75+</div>
                        <div class="text-gray-600">Approved Projects</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold gradient-text mb-2">25+</div>
                        <div class="text-gray-600">Team Members</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold gradient-text mb-2">500+</div>
                        <div class="text-gray-600">Collaborations</div>
                    </div>
                @else
                    <div>
                        <div class="text-4xl font-bold gradient-text mb-2">
                            {{ \App\Models\Tenant::count() }}+
                        </div>
                        <div class="text-gray-600">Active Tenants</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold gradient-text mb-2">
                            {{ \App\Models\Tenant::where('status', 'active')->count() }}+
                        </div>
                        <div class="text-gray-600">Active Subscriptions</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold gradient-text mb-2">
                            {{ \App\Models\User::withoutGlobalScope('tenant')->count() }}+
                        </div>
                        <div class="text-gray-600">Total Users</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold gradient-text mb-2">99.9%</div>
                        <div class="text-gray-600">Uptime</div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Powerful Features for Modern Teams
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Everything you need to manage your innovation pipeline from idea submission to final approval.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature Cards (same as before) -->
                <div class="bg-white p-8 rounded-xl card-hover">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">KPI Dashboard</h3>
                    <p class="text-gray-600">
                        Track total ideas, pending reviews, pricing requests, and approved budgets at a glance.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-xl card-hover">
                    <div class="feature-icon">
                        <i class="fas fa-stream"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Innovation Pipeline</h3>
                    <p class="text-gray-600">
                        Centralized grid view with live search, filtering, sorting, and responsive design.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-xl card-hover">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Team Collaboration</h3>
                    <p class="text-gray-600">
                        Role-based permissions with in-grid editing and team commenting system.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-xl card-hover">
                    <div class="feature-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Activity Log</h3>
                    <p class="text-gray-600">
                        Complete audit trail tracking all changes with timestamp and user details.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-xl card-hover">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Secure System</h3>
                    <p class="text-gray-600">
                        Invitation-only access with custom role assignment and advanced permissions.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-xl card-hover">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Mobile Friendly</h3>
                    <p class="text-gray-600">
                        Fully responsive with automatic card view on mobile devices.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} Innovation Pipeline. All rights reserved.</p>
                @if($tenant)
                    <p class="mt-2 text-sm text-gray-400">
                        Tenant: {{ $tenant->subdomain }} | Expires: {{ $tenant->expires_at ? $tenant->expires_at->format('M d, Y') : 'Never' }}
                    </p>
                @endif
            </div>
        </div>
    </footer>

</body>
</html>
