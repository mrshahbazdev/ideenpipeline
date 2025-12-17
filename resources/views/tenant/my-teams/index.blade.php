<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Teams - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50" x-data="{ mobileMenuOpen: false }">

    @include('tenant.partials.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-users text-indigo-600 mr-3"></i>
                My Teams
            </h1>
            <p class="text-gray-600 mt-2">Manage your team memberships</p>
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

        <!-- Current Active Team -->
        @if($currentTeam)
            <div class="mb-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white font-bold text-2xl shadow-lg" 
                             style="background: {{ $currentTeam->color }}">
                            {{ $currentTeam->initials }}
                        </div>
                        <div>
                            <p class="text-indigo-100 text-sm mb-1">
                                <i class="fas fa-star mr-1"></i>Active Team
                            </p>
                            <h3 class="text-2xl font-bold">{{ $currentTeam->name }}</h3>
                            <p class="text-indigo-100 text-sm mt-1">
                                <i class="fas fa-users mr-1"></i>{{ $currentTeam->member_count }} members
                                <span class="mx-2">•</span>
                                <i class="fas fa-lightbulb mr-1"></i>{{ $currentTeam->ideas_count }} ideas
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- My Teams -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-user-check text-green-600 mr-2"></i>
                My Teams ({{ $myTeams->count() }})
            </h2>
            
            @if($myTeams->isEmpty())
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Teams Yet</h3>
                    <p class="text-gray-600 mb-6">Join a team below to get started!</p>
                </div>
            @else
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($myTeams as $team)
                        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white font-bold shadow-lg" 
                                         style="background: {{ $team->color }}">
                                        {{ $team->initials }}
                                    </div>
                                    @if($currentTeam && $currentTeam->id === $team->id)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">
                                            <i class="fas fa-check mr-1"></i>Active
                                        </span>
                                    @endif
                                </div>

                                <h3 class="font-bold text-gray-900 text-lg mb-2">{{ $team->name }}</h3>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $team->description }}</p>

                                <div class="flex items-center space-x-4 text-xs text-gray-600 mb-4">
                                    <span>
                                        <i class="fas fa-users mr-1"></i>{{ $team->member_count }}
                                    </span>
                                    <span>
                                        <i class="fas fa-lightbulb mr-1"></i>{{ $team->ideas_count }}
                                    </span>
                                </div>

                                <div class="flex items-center space-x-2">
                                    @if(!$currentTeam || $currentTeam->id !== $team->id)
                                        <form method="POST" action="{{ route('tenant.teams.switch', ['tenantId' => $tenant->id, 'team' => $team->id]) }}" class="flex-1">
                                            @csrf
                                            <button type="submit" 
                                                    class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-semibold">
                                                <i class="fas fa-exchange-alt mr-1"></i>Switch To
                                            </button>
                                        </form>
                                    @else
                                        <div class="flex-1 px-4 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-semibold text-center">
                                            <i class="fas fa-check mr-1"></i>Active
                                        </div>
                                    @endif
                                    
                                    <form method="POST" action="{{ route('tenant.teams.leave', ['tenantId' => $tenant->id, 'team' => $team->id]) }}"
                                          onsubmit="return confirm('Leave {{ $team->name }}?')">
                                        @csrf
                                        <button type="submit"
                                                class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition text-sm">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Available Teams -->
        <div>
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                Available Teams ({{ $availableTeams->count() }})
            </h2>
            
            @if($availableTeams->isEmpty())
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <i class="fas fa-check-circle text-green-300 text-4xl mb-3"></i>
                    <p class="text-gray-600">You're already a member of all active teams!</p>
                </div>
            @else
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($availableTeams as $team)
                        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition overflow-hidden border-2 border-dashed border-gray-200">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white font-bold shadow-lg" 
                                         style="background: {{ $team->color }}">
                                        {{ $team->initials }}
                                    </div>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full">
                                        New
                                    </span>
                                </div>

                                <h3 class="font-bold text-gray-900 text-lg mb-2">{{ $team->name }}</h3>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $team->description }}</p>

                                <div class="flex items-center space-x-4 text-xs text-gray-600 mb-4">
                                    <span>
                                        <i class="fas fa-users mr-1"></i>{{ $team->members_count }} members
                                    </span>
                                </div>

                                <form method="POST" action="{{ route('tenant.teams.join', ['tenantId' => $tenant->id, 'team' => $team->id]) }}">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition font-semibold shadow-lg">
                                        <i class="fas fa-plus mr-2"></i>Join Team
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>
