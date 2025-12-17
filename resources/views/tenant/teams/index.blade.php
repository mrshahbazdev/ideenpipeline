<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teams - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

    <!-- Navigation -->
    @include('tenant.partials.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Teams Management</h1>
                <p class="text-gray-600 mt-2">Organize your team members into groups</p>
            </div>
            <a href="{{ route('tenant.teams.create', ['tenantId' => $tenant->id]) }}" class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-2"></i>Create Team
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Stats -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Teams</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_teams'] }}</p>
                    </div>
                    <i class="fas fa-users text-blue-500 text-3xl"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Active Teams</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['active_teams'] }}</p>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-3xl"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Members</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['total_members'] }}</p>
                    </div>
                    <i class="fas fa-user-friends text-purple-500 text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Teams Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($teams as $team)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                    <!-- Team Header -->
                    <div class="p-6" style="background: linear-gradient(135deg, {{ $team->color }}15 0%, {{ $team->color }}30 100%);">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white font-bold text-xl" style="background: {{ $team->color }}">
                                {{ strtoupper(substr($team->name, 0, 1)) }}
                            </div>
                            @if($team->is_active)
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                    <i class="fas fa-check-circle"></i> Active
                                </span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">
                                    <i class="fas fa-pause-circle"></i> Inactive
                                </span>
                            @endif
                        </div>
                        
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $team->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $team->description ?: 'No description' }}</p>
                    </div>

                    <!-- Team Stats -->
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">
                                <i class="fas fa-users text-gray-400 mr-2"></i>
                                {{ $team->members_count }} {{ Str::plural('member', $team->members_count) }}
                            </span>
                            <span class="text-gray-600">
                                <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                {{ $team->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-6 py-4 bg-white border-t border-gray-100 flex items-center justify-between">
                        <a href="{{ route('tenant.teams.show', ['tenantId' => $tenant->id, 'team' => $team->id]) }}" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                            <i class="fas fa-eye mr-1"></i>View Details
                        </a>
                        <div class="flex space-x-2">
                            <a href="{{ route('tenant.teams.edit', ['tenantId' => $tenant->id, 'team' => $team->id]) }}" class="text-gray-600 hover:text-indigo-600">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('tenant.teams.destroy', ['tenantId' => $tenant->id, 'team' => $team->id]) }}" onsubmit="return confirm('Delete this team?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-600 hover:text-red-600">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 bg-white rounded-lg shadow">
                    <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No teams yet</h3>
                    <p class="text-gray-600 mb-6">Create your first team to organize your members</p>
                    <a href="{{ route('tenant.teams.create', ['tenantId' => $tenant->id]) }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i>Create First Team
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($teams->hasPages())
            <div class="mt-8">
                {{ $teams->links() }}
            </div>
        @endif

    </div>

</body>
</html>
