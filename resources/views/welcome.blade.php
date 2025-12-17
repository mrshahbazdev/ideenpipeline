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
    
    <!-- Custom Styles -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
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
                    @if($siteLogo = \App\Models\Setting::where('key', 'site_logo')->value('value'))
                        <img src="{{ Storage::url($siteLogo) }}" alt="{{ config('app.name') }}" class="h-10">
                    @else
                        <h1 class="text-2xl font-bold gradient-text">Innovation Pipeline</h1>
                    @endif
                    
                    @if(isset($tenant))
                        <span class="ml-4 tenant-subdomain">
                            {{ $tenant->subdomain }}
                        </span>
                    @endif
                </div>
                
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-red-600 font-medium">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
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
                    @if(isset($tenant))
                        <div class="inline-block bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-semibold mb-6">
                            <i class="fas fa-building mr-2"></i>{{ $tenant->admin_name }}'s Innovation Hub
                        </div>
                    @endif
                    
                    <h1 class="text-5xl md:text-6xl font-bold leading-tight mb-6">
                        Transform Ideas Into Reality
                    </h1>
                    <p class="text-xl text-white/90 mb-8">
                        Centralize, manage, and execute all your innovative project ideas with our powerful Innovation Pipeline platform. Built for teams who turn dreams into deliverables.
                    </p>
                    
                    <div class="flex flex-wrap gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-primary">
                                <i class="fas fa-rocket mr-2"></i>Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary">
                                <i class="fas fa-sign-in-alt mr-2"></i>Get Started
                            </a>
                            <a href="{{ route('submit-idea') }}" class="bg-white text-indigo-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition">
                                <i class="fas fa-lightbulb mr-2"></i>Submit an Idea
                            </a>
                        @endauth
                    </div>
                    
                    @if(isset($tenant))
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
                <div>
                    <div class="text-4xl font-bold gradient-text mb-2">
                        {{ \App\Models\Idea::count() }}+
                    </div>
                    <div class="text-gray-600">Ideas Managed</div>
                </div>
                <div>
                    <div class="text-4xl font-bold gradient-text mb-2">
                        {{ \App\Models\Idea::where('status', 'approved')->count() }}+
                    </div>
                    <div class="text-gray-600">Approved Projects</div>
                </div>
                <div>
                    <div class="text-4xl font-bold gradient-text mb-2">
                        {{ \App\Models\User::count() }}+
                    </div>
                    <div class="text-gray-600">Team Members</div>
                </div>
                <div>
                    <div class="text-4xl font-bold gradient-text mb-2">
                        {{ \App\Models\Comment::count() }}+
                    </div>
                    <div class="text-gray-600">Collaborations</div>
                </div>
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
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-xl card-hover">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">KPI Dashboard</h3>
                    <p class="text-gray-600">
                        Track total ideas, pending reviews, pricing requests, and approved budgets at a glance.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-xl card-hover">
                    <div class="feature-icon">
                        <i class="fas fa-stream"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Innovation Pipeline</h3>
                    <p class="text-gray-600">
                        Centralized grid view with live search, filtering, sorting, and responsive design.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-xl card-hover">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Team Collaboration</h3>
                    <p class="text-gray-600">
                        Role-based permissions with in-grid editing and team commenting system.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white p-8 rounded-xl card-hover">
                    <div class="feature-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Activity Log</h3>
                    <p class="text-gray-600">
                        Complete audit trail tracking all changes with timestamp and user details.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white p-8 rounded-xl card-hover">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Secure System</h3>
                    <p class="text-gray-600">
                        Invitation-only access with custom role assignment and advanced permissions.
                    </p>
                </div>

                <!-- Feature 6 -->
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

    <!-- Workflow Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Simple Yet Powerful Workflow
                </h2>
                <p class="text-xl text-gray-600">
                    From idea submission to execution in 4 easy steps
                </p>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">
                        1
                    </div>
                    <h3 class="font-bold text-lg mb-2">Submit Idea</h3>
                    <p class="text-gray-600 text-sm">Team members or clients submit ideas via the public form</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">
                        2
                    </div>
                    <h3 class="font-bold text-lg mb-2">Review & Prioritize</h3>
                    <p class="text-gray-600 text-sm">Work-Bees team reviews and sets priority scores</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">
                        3
                    </div>
                    <h3 class="font-bold text-lg mb-2">Get Pricing</h3>
                    <p class="text-gray-600 text-sm">Developers provide technical solutions and cost estimates</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">
                        4
                    </div>
                    <h3 class="font-bold text-lg mb-2">Approve & Execute</h3>
                    <p class="text-gray-600 text-sm">Admin approves budget and project moves to execution</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 gradient-bg text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold mb-6">
                Ready to Transform Your Innovation Process?
            </h2>
            <p class="text-xl mb-8 text-white/90">
                Join teams who are already managing their innovation pipeline efficiently.
            </p>
            <div class="flex justify-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-white text-indigo-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition">
                        <i class="fas fa-rocket mr-2"></i>Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="bg-white text-indigo-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login Now
                    </a>
                    <a href="{{ route('submit-idea') }}" class="bg-white/20 backdrop-blur-sm text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/30 transition">
                        <i class="fas fa-lightbulb mr-2"></i>Submit an Idea
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Innovation Pipeline</h3>
                    <p class="text-gray-400">
                        Empowering teams to manage and execute innovative ideas efficiently.
                    </p>
                </div>
                
                @if(isset($tenant))
                <div>
                    <h3 class="text-xl font-bold mb-4">Tenant Info</h3>
                    <p class="text-gray-400">
                        <strong>Subdomain:</strong> {{ $tenant->subdomain }}<br>
                        <strong>Admin:</strong> {{ $tenant->admin_name }}<br>
                        <strong>Email:</strong> {{ $tenant->admin_email }}
                    </p>
                </div>
                @endif
                
                <div>
                    <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-white">Dashboard</a></li>
                        <li><a href="{{ route('submit-idea') }}" class="hover:text-white">Submit Idea</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white">Login</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Innovation Pipeline. All rights reserved.</p>
                @if(isset($tenant))
                    <p class="mt-2 text-sm">
                        Tenant expires: {{ $tenant->expires_at ? $tenant->expires_at->format('M d, Y') : 'Never' }}
                    </p>
                @endif
            </div>
        </div>
    </footer>

</body>
</html>
