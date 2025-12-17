<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Teams - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .team-card {
            transition: all 0.3s ease;
        }
        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .active-team {
            border: 3px solid #10B981;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ mobileMenuOpen: false }">

    @include('tenant.partials.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Teams</h1>
            <p class="text-gray-600 mt-2">Collaborate with different teams</p>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- Current Active Team -->
        @if($currentTeam)
            <div class="mb-8 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white font-bold text-2xl shadow-lg" 
                             style="background: {{ $currentTeam->color }}">
                            {{ strtoupper(substr($currentTeam->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm text-green-700 font-semibold mb-1">
                                <i class="fas fa-check-circle mr-1"></i>ACTIVE TEAM
                            </p>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $currentTeam->name }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $currentTeam->description }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Team Members</p>
                        <p class="text-3xl font-bold text-green-600">{{ $currentTeam->member_count }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- My Teams Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-users text-indigo-600 mr-2"></i>
                    Teams I'm In ({{ $myTeams->count() }})
                </h2>
            </div>

            @if($myTeams->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($myTeams as $team)
                        <div class="team-card bg-white rounded-xl shadow-lg overflow-hidden {{ $currentTeam && $currentTeam->id === $team->id ? 'active-team' : '' }}">
                            <!-- Team Header -->
                            <div class="p-6" style="background: linear-gradient(135deg, {{ $team->color }}15 0%, {{ $team->color }}30 100%);">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-14 h-14 rounded-lg flex items-center justify-center text-white font-bold text-xl" 
                                         style="background: {{ $team->color }}">
                                        {{ strtoupper(substr($team->name, 0, 1)) }}
                                    </div>
                                    @if($currentTeam && $currentTeam->id === $team->id)
                                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                            <i class="fas fa-check-circle"></i> Active
                                        </span>
                                    @endif
                                </div>
                                
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $team->name }}</h3>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ $team->description ?: 'No description' }}</p>
                            </div>

                            <!-- Team Stats -->
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">
                                        <i class="fas fa-users text-gray-400 mr-1"></i>
                                        {{ $team->member_count }} members
                                    </span>
                                    <span class="text-gray-600">
                                        <i class="fas fa-calendar text-gray-400 mr-1"></i>
                                        Joined {{ $team->pivot->joined_at instanceof \Carbon\Carbon ? $team->pivot->joined_at->diffForHumans() : \Carbon\Carbon::parse($team->pivot->joined_at)->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="px-6 py-4 bg-white border-t border-gray-200 flex gap-2">
                                @if(!$currentTeam || $currentTeam->id !== $team->id)
                                    <form method="POST" action="{{ route('tenant.teams.switch', ['tenantId' => $tenant->id, 'team' => $team->id]) }}" class="flex-1">
                                        @csrf
                                        <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-semibold">
                                            <i class="fas fa-exchange-alt mr-1"></i>Switch to This Team
                                        </button>
                                    </form>
                                @else
                                    <button disabled class="flex-1 px-4 py-2 bg-green-100 text-green-800 rounded-lg cursor-not-allowed text-sm font-semibold">
                                        <i class="fas fa-check mr-1"></i>Currently Active
                                    </button>
                                @endif

                                <form method="POST" action="{{ route('tenant.my-teams.leave', ['tenantId' => $tenant->id, 'team' => $team->id]) }}"
                                      onsubmit="return confirm('Leave {{ $team->name }}?')">
                                    @csrf
                                    <button class="px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition text-sm">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">You're not in any team yet</h3>
                    <p class="text-gray-600">Join a team below to start collaborating</p>
                </div>
            @endif
        </div>

        <!-- Available Teams Section -->
        @if($availableTeams->count() > 0)
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-plus-circle text-green-600 mr-2"></i>
                        Available Teams ({{ $availableTeams->count() }})
                    </h2>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($availableTeams as $team)
                        <div class="team-card bg-white rounded-xl shadow-lg overflow-hidden">
                            <!-- Team Header -->
                            <div class="p-6" style="background: linear-gradient(135deg, {{ $team->color }}15 0%, {{ $team->color }}30 100%);">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-14 h-14 rounded-lg flex items-center justify-center text-white font-bold text-xl" 
                                         style="background: {{ $team->color }}">
                                        {{ strtoupper(substr($team->name, 0, 1)) }}
                                    </div>
                                </div>
                                
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $team->name }}</h3>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ $team->description ?: 'No description' }}</p>
                            </div>

                            <!-- Team Stats -->
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">
                                        <i class="fas fa-users text-gray-400 mr-1"></i>
                                        {{ $team->members_count }} members
                                    </span>
                                    <span class="text-gray-600">
                                        <i class="fas fa-user text-gray-400 mr-1"></i>
                                        by {{ $team->creator->name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Join Action -->
                            <div class="px-6 py-4 bg-white border-t border-gray-200">
                                <form method="POST" action="{{ route('tenant.my-teams.join', ['tenantId' => $tenant->id, 'team' => $team->id]) }}">
                                    @csrf
                                    <button class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                                        <i class="fas fa-user-plus mr-2"></i>Join This Team
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Info Card -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-500 text-2xl mr-4"></i>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-2">About Teams</h4>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li><i class="fas fa-check text-blue-500 mr-2"></i>Join multiple teams to collaborate on different projects</li>
                        <li><i class="fas fa-check text-blue-500 mr-2"></i>Switch between teams to work on team-specific ideas</li>
                        <li><i class="fas fa-check text-blue-500 mr-2"></i>Your active team determines which ideas you see and submit</li>
                        <li><i class="fas fa-check text-blue-500 mr-2"></i>Leave teams anytime if you no longer need access</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
