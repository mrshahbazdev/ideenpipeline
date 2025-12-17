<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ideas - {{ $currentTeam->name }} - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .idea-card {
            transition: all 0.3s ease;
        }
        .idea-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ mobileMenuOpen: false, filterOpen: false }">

    @include('tenant.partials.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-lightbulb text-yellow-500 mr-3"></i>
                        Team Ideas
                    </h1>
                    <p class="text-gray-600 mt-2">Share and collaborate on innovative ideas</p>
                </div>
                <a href="{{ route('tenant.ideas.create', ['tenantId' => $tenant->id]) }}" 
                   class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>Submit New Idea
                </a>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
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

        <!-- Current Team Banner -->
        <div class="mb-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white font-bold text-2xl shadow-lg" 
                         style="background: {{ $currentTeam->color }}">
                        {{ strtoupper(substr($currentTeam->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm text-indigo-100 mb-1">
                            <i class="fas fa-users mr-1"></i>Active Team
                        </p>
                        <h3 class="text-2xl font-bold">{{ $currentTeam->name }}</h3>
                        <p class="text-sm text-indigo-100 mt-1">{{ $currentTeam->member_count }} members</p>
                    </div>
                </div>
                <a href="{{ route('tenant.my-teams', ['tenantId' => $tenant->id]) }}" 
                   class="px-4 py-2 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-indigo-50 transition">
                    <i class="fas fa-exchange-alt mr-2"></i>Switch Team
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Ideas</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_ideas'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-lightbulb text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Pending Review</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Approved</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['approved'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">My Ideas</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['my_ideas'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <div class="relative">
                        <input 
                            type="text" 
                            placeholder="Search ideas..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        >
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex items-center space-x-3">
                    <!-- Status Filter -->
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="in-review">In Review</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="implemented">Implemented</option>
                    </select>

                    <!-- Priority Filter -->
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>

                    <!-- Sort -->
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="latest">Latest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="most-voted">Most Voted</option>
                        <option value="least-voted">Least Voted</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Ideas Grid -->
        @if($ideas->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($ideas as $idea)
                    <div class="idea-card bg-white rounded-xl shadow-lg overflow-hidden">
                        <!-- Idea Header -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                                        {{ $idea->title }}
                                    </h3>
                                </div>
                            </div>

                            <!-- Status & Priority Badges -->
                            <div class="flex items-center space-x-2 mb-3">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $idea->getStatusBadgeClass() }}">
                                    {{ ucfirst($idea->status) }}
                                </span>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $idea->getPriorityBadgeClass() }}">
                                    <i class="fas fa-flag mr-1"></i>{{ ucfirst($idea->priority) }}
                                </span>
                            </div>

                            <!-- Description Preview -->
                            <p class="text-sm text-gray-600 line-clamp-3">
                                {{ $idea->description }}
                            </p>
                        </div>

                        <!-- Idea Footer -->
                        <div class="px-6 py-4 bg-gray-50">
                            <div class="flex items-center justify-between mb-3">
                                <!-- Author -->
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-semibold text-xs
                                        {{ $idea->user->role === 'admin' ? 'bg-red-500' : '' }}
                                        {{ $idea->user->role === 'developer' ? 'bg-purple-500' : '' }}
                                        {{ $idea->user->role === 'work-bee' ? 'bg-green-500' : '' }}
                                        {{ $idea->user->role === 'standard' ? 'bg-blue-500' : '' }}
                                    ">
                                        {{ strtoupper(substr($idea->user->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-2">
                                        <p class="text-xs font-medium text-gray-900">{{ $idea->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $idea->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>

                                <!-- Votes -->
                                <!-- Votes -->
                                <div class="flex items-center space-x-2">
                                    <button onclick="cardVote({{ $idea->id }}, event)" 
                                            data-idea-id="{{ $idea->id }}"
                                            data-has-voted="{{ $idea->hasVoted($user) ? 'true' : 'false' }}"
                                            class="flex items-center px-3 py-1 rounded-lg hover:bg-indigo-200 transition
                                            {{ $idea->hasVoted($user) ? 'bg-green-100 text-green-600' : 'bg-indigo-100 text-indigo-600' }}">
                                        <i class="fas fa-arrow-up mr-1"></i>
                                        <span class="font-semibold vote-count-{{ $idea->id }}">{{ $idea->votes }}</span>
                                    </button>
                                </div>

                            </div>

                            <!-- Tags -->
                            @if($idea->tags && count($idea->tags) > 0)
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @foreach(array_slice($idea->tags, 0, 3) as $tag)
                                        <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded">
                                            #{{ $tag }}
                                        </span>
                                    @endforeach
                                    @if(count($idea->tags) > 3)
                                        <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded">
                                            +{{ count($idea->tags) - 3 }} more
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <!-- View Details Button -->
                            <a href="{{ route('tenant.ideas.show', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" 
                               class="block w-full px-4 py-2 bg-white border border-indigo-600 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition text-center font-semibold text-sm">
                                <i class="fas fa-eye mr-2"></i>View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($ideas->hasPages())
                <div class="flex justify-center">
                    {{ $ideas->links() }}
                </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <i class="fas fa-lightbulb text-gray-300 text-6xl mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No Ideas Yet</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Be the first to share an innovative idea with your team. Every great innovation starts with a single idea!
                </p>
                <a href="{{ route('tenant.ideas.create', ['tenantId' => $tenant->id]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg">
                    <i class="fas fa-plus mr-2"></i>Submit First Idea
                </a>
            </div>
        @endif

        <!-- Help Card -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-500 text-2xl mr-4"></i>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-2">How Ideas Work</h4>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li><i class="fas fa-check text-blue-500 mr-2"></i>Submit ideas specific to your current active team</li>
                        <li><i class="fas fa-check text-blue-500 mr-2"></i>Team members can view and vote on ideas</li>
                        <li><i class="fas fa-check text-blue-500 mr-2"></i>Admins can review and approve ideas for implementation</li>
                        <li><i class="fas fa-check text-blue-500 mr-2"></i>Switch teams to see different idea pipelines</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
        <script>
    function cardVote(ideaId, event) {
        event.preventDefault();
        const button = event.currentTarget;
        const csrfToken = '{{ csrf_token() }}';
        
        button.disabled = true;
        
        fetch(`/tenant/{{ $tenant->id }}/ideas/${ideaId}/vote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`.vote-count-${ideaId}`).textContent = data.voteCount;
                
                if (data.hasVoted) {
                    button.classList.remove('bg-indigo-100', 'text-indigo-600');
                    button.classList.add('bg-green-100', 'text-green-600');
                } else {
                    button.classList.remove('bg-green-100', 'text-green-600');
                    button.classList.add('bg-indigo-100', 'text-indigo-600');
                }
            }
        })
        .finally(() => {
            button.disabled = false;
        });
    }
</script>

</body>
</html>
