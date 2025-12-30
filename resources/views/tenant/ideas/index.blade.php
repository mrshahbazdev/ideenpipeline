<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ideen - {{ $currentTeam->name }} - {{ $tenant->subdomain }}</title>
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-white">
        
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-lightbulb text-yellow-500 mr-3"></i>
                        Team-Ideen
                    </h1>
                    <p class="text-gray-600 mt-2">Innovative Ideen teilen und gemeinsam weiterentwickeln</p>
                </div>
                <a href="{{ route('tenant.ideas.create', ['tenantId' => $tenant->id]) }}" 
                   class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg transform hover:scale-105 text-center">
                    <i class="fas fa-plus mr-2 text-white"></i>Neue Idee einreichen
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm text-white">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 text-white"></i>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="mb-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white text-white">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white font-bold text-2xl shadow-lg" 
                         style="background: {{ $currentTeam->color }}">
                        {{ strtoupper(substr($currentTeam->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm text-indigo-100 mb-1">
                            <i class="fas fa-users mr-1 text-white"></i>Aktives Team
                        </p>
                        <h3 class="text-2xl font-bold">{{ $currentTeam->name }}</h3>
                        <p class="text-sm text-indigo-100 mt-1 text-white">{{ $currentTeam->member_count }} Mitglieder</p>
                    </div>
                </div>
                <a href="{{ route('tenant.my-teams', ['tenantId' => $tenant->id]) }}" 
                   class="px-4 py-2 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-indigo-50 transition text-center">
                    <i class="fas fa-exchange-alt mr-2"></i>Team wechseln
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8 text-white">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Ideen gesamt</p>
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
                        <p class="text-sm text-gray-600 mb-1">In Prüfung</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between text-white">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Genehmigt</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['approved'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-white">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Meine Ideen</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['my_ideas'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-white">
                <div class="flex-1">
                    <div class="relative">
                        <input 
                            type="text" 
                            placeholder="Ideen suchen..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                        >
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-700">
                        <option value="">Alle Status</option>
                        <option value="pending">Offen</option>
                        <option value="in-review">In Prüfung</option>
                        <option value="approved">Genehmigt</option>
                        <option value="rejected">Abgelehnt</option>
                        <option value="implemented">Umgesetzt</option>
                    </select>

                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-700">
                        <option value="">Alle Prioritäten</option>
                        <option value="low">Niedrig</option>
                        <option value="medium">Mittel</option>
                        <option value="high">Hoch</option>
                        <option value="urgent">Dringend</option>
                    </select>

                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-700">
                        <option value="latest">Neueste zuerst</option>
                        <option value="oldest">Älteste zuerst</option>
                        <option value="most-voted">Beliebteste</option>
                        <option value="least-voted">Weniger beliebt</option>
                    </select>
                </div>
            </div>
        </div>

        @if($ideas->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 text-white">
                @foreach($ideas as $idea)
                    <div class="idea-card bg-white rounded-xl shadow-lg overflow-hidden flex flex-col">
                        <div class="p-6 border-b border-gray-200 flex-1">
                            <div class="flex items-start justify-between mb-3 text-white">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                                        {{ $idea->title }}
                                    </h3>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 mb-3">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $idea->getStatusBadgeClass() }}">
                                    {{ __($idea->status) }}
                                </span>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $idea->getPriorityBadgeClass() }}">
                                    <i class="fas fa-flag mr-1"></i>{{ __($idea->priority) }}
                                </span>
                            </div>

                            <p class="text-sm text-gray-600 line-clamp-3">
                                {{ $idea->description }}
                            </p>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 mt-auto text-white">
                            <div class="flex items-center justify-between mb-3">
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
                                        <p class="text-[10px] text-gray-500">{{ $idea->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <button onclick="cardVote({{ $idea->id }}, event)" 
                                            class="flex items-center px-3 py-1 rounded-lg hover:bg-indigo-200 transition
                                            {{ $idea->hasVoted($user) ? 'bg-green-100 text-green-600' : 'bg-indigo-100 text-indigo-600' }}">
                                        <i class="fas fa-arrow-up mr-1 text-white"></i>
                                        <span class="font-semibold vote-count-{{ $idea->id }} text-white">{{ $idea->votes }}</span>
                                    </button>
                                </div>
                            </div>

                            @if($idea->tags && count($idea->tags) > 0)
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @foreach(array_slice($idea->tags, 0, 2) as $tag)
                                        <span class="px-2 py-0.5 bg-gray-200 text-gray-700 text-[10px] rounded">
                                            #{{ $tag }}
                                        </span>
                                    @endforeach
                                    @if(count($idea->tags) > 2)
                                        <span class="text-[10px] text-gray-400">+{{ count($idea->tags) - 2 }}</span>
                                    @endif
                                </div>
                            @endif

                            <a href="{{ route('tenant.ideas.show', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" 
                               class="block w-full px-4 py-2 bg-white border border-indigo-600 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition text-center font-semibold text-sm">
                                <i class="fas fa-eye mr-2"></i>Details ansehen
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($ideas->hasPages())
                <div class="flex justify-center">
                    {{ $ideas->links() }}
                </div>
            @endif

        @else
            <div class="bg-white rounded-xl shadow-lg p-12 text-center text-white">
                <i class="fas fa-lightbulb text-gray-300 text-6xl mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Noch keine Ideen vorhanden</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Seien Sie der Erste, der eine innovative Idee mit Ihrem Team teilt. Jede große Innovation beginnt mit einem einzigen Gedanken!
                </p>
                <a href="{{ route('tenant.ideas.create', ['tenantId' => $tenant->id]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg">
                    <i class="fas fa-plus mr-2 text-white"></i>Erste Idee einreichen
                </a>
            </div>
        @endif

        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6 text-white">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-500 text-2xl mr-4 text-white"></i>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-2">So funktionieren Ideen</h4>
                    <ul class="text-sm text-blue-800 space-y-2 text-white">
                        <li><i class="fas fa-check text-blue-500 mr-2 text-white"></i>Reichen Sie Ideen speziell für Ihr aktuell aktives Team ein.</li>
                        <li><i class="fas fa-check text-blue-500 mr-2 text-white"></i>Teammitglieder können Ideen einsehen und für sie abstimmen (Voting).</li>
                        <li><i class="fas fa-check text-blue-500 mr-2 text-white"></i>Admins können Ideen prüfen und für die Umsetzung freigeben.</li>
                        <li><i class="fas fa-check text-blue-500 mr-2 text-white"></i>Wechseln Sie die Teams, um verschiedene Ideen-Pipelines zu sehen.</li>
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