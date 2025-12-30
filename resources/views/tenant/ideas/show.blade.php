<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $idea->title }} - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .vote-button {
            transition: all 0.3s ease;
        }
        .vote-button:hover {
            transform: scale(1.1);
        }
        .vote-button.active {
            transform: scale(1.15);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ mobileMenuOpen: false, commentOpen: false }">

    @include('tenant.partials.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-6">
            <a href="{{ route('tenant.ideas.index', ['tenantId' => $tenant->id]) }}" 
               class="inline-flex items-center text-gray-600 hover:text-gray-900 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Zurück zur Übersicht
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
                    <div>
                        <p class="text-green-800 font-semibold">{{ session('success') }}</p>
                        <p class="text-green-600 text-sm">Der Status wurde erfolgreich aktualisiert.</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-2xl mr-3"></i>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-8 border-b border-gray-200">
                        <div class="flex items-center space-x-2 mb-4">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $idea->getStatusBadgeClass() }}">
                                <i class="fas fa-circle mr-1 text-xs"></i>{{ ucfirst(str_replace('-', ' ', $idea->status)) }}
                            </span>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $idea->getPriorityBadgeClass() }}">
                                <i class="fas fa-flag mr-1"></i>{{ ucfirst($idea->priority) }} Priorität
                            </span>
                        </div>

                        <h1 class="text-3xl font-bold text-gray-900 mb-4">
                            {{ $idea->title }}
                        </h1>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold
                                    {{ $idea->user->role === 'admin' ? 'bg-red-500' : '' }}
                                    {{ $idea->user->role === 'developer' ? 'bg-purple-500' : '' }}
                                    {{ $idea->user->role === 'work-bee' ? 'bg-green-500' : '' }}
                                    {{ $idea->user->role === 'standard' ? 'bg-blue-500' : '' }}
                                ">
                                    {{ strtoupper(substr($idea->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $idea->user->name }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $idea->created_at->format('d.m.Y \u\m H:i') }} Uhr
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3">
                                <button 
                                    id="voteButton"
                                    data-idea-id="{{ $idea->id }}"
                                    data-has-voted="{{ $idea->hasVoted($user) ? 'true' : 'false' }}"
                                    class="vote-button flex flex-col items-center px-6 py-3 rounded-lg transition shadow-lg font-semibold transform hover:scale-105
                                    {{ $idea->hasVoted($user) ? 'bg-green-600 hover:bg-green-700' : 'bg-indigo-600 hover:bg-indigo-700' }} text-white"
                                    onclick="toggleVote()">
                                    <i class="fas fa-arrow-up text-2xl mb-1"></i>
                                    <span class="font-bold text-lg" id="voteCount">{{ $idea->votes }}</span>
                                    <span class="text-xs" id="voteText">{{ $idea->hasVoted($user) ? 'Gevotet' : 'Voten' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">
                            <i class="fas fa-align-left text-indigo-600 mr-2"></i>
                            Beschreibung
                        </h2>
                        <div class="prose max-w-none">
                            <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $idea->description }}</p>
                        </div>
                    </div>

                    @if($idea->tags && count($idea->tags) > 0)
                        <div class="px-8 pb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-3">
                                <i class="fas fa-tags text-indigo-600 mr-2"></i>
                                Schlagworte
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($idea->tags as $tag)
                                    <span class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-full text-sm font-semibold">
                                        #{{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="px-8 pb-8 border-t border-gray-200 pt-6">
                        <div class="flex flex-wrap items-center gap-3">
                            
                            @if($idea->canEditBasic($user) || $idea->canEditDeveloper($user) || $idea->canEditWorkBee($user))
                                <a href="{{ route('tenant.ideas.edit', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" 
                                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold shadow-lg">
                                    <i class="fas fa-edit mr-2 text-white"></i>
                                    Idee bearbeiten
                                    @if($user->isDeveloper())
                                        <span class="text-xs opacity-80">(Entwickler)</span>
                                    @elseif($user->isWorkBee())
                                        <span class="text-xs opacity-80">(Work-Bee)</span>
                                    @elseif($user->isAdmin())
                                        <span class="text-xs opacity-80">(Admin)</span>
                                    @endif
                                </a>
                            @endif

                            @if($user->isAdmin())
                                <div class="flex flex-wrap items-center gap-3 md:ml-auto">
                                    @if($idea->status !== 'approved')
                                        <form method="POST" action="{{ route('tenant.ideas.update-status', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold shadow-lg" onclick="return confirm('Diese Idee genehmigen?')">
                                                <i class="fas fa-check mr-2 text-white"></i>Genehmigen
                                            </button>
                                        </form>
                                    @endif

                                    @if($idea->status !== 'in-review')
                                        <form method="POST" action="{{ route('tenant.ideas.update-status', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" class="inline text-white">
                                            @csrf
                                            <input type="hidden" name="status" value="in-review">
                                            <button type="submit" class="px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold shadow-lg">
                                                <i class="fas fa-eye mr-2 text-white"></i>Prüfen
                                            </button>
                                        </form>
                                    @endif

                                    @if($idea->status !== 'rejected')
                                        <form method="POST" action="{{ route('tenant.ideas.update-status', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" class="inline text-white">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold shadow-lg" onclick="return confirm('Diese Idee ablehnen?')">
                                                <i class="fas fa-times mr-2 text-white"></i>Ablehnen
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg overflow-hidden text-white">
                    <div class="p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center justify-between">
                            <span>
                                <i class="fas fa-comments text-indigo-600 mr-2 text-white"></i>
                                Diskussion (<span id="commentsCount">{{ $idea->comments()->count() }}</span>)
                            </span>
                            <button @click="commentOpen = !commentOpen" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm">
                                <i class="fas fa-plus mr-2 text-white"></i>Kommentar hinzufügen
                            </button>
                        </h2>

                        <div x-show="commentOpen" x-transition class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200" style="display: none;">
                            <form id="commentForm" onsubmit="submitComment(event)">
                                <textarea id="commentInput" rows="3" placeholder="Teilen Sie Ihre Gedanken zu dieser Idee..." maxlength="1000" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 mb-3 text-gray-900"></textarea>
                                <div class="flex justify-between items-center text-white">
                                    <span class="text-xs text-gray-500"><span id="charCount">0</span> / 1000 Zeichen</span>
                                    <div class="flex space-x-2 text-white">
                                        <button type="button" @click="commentOpen = false" class="px-4 py-2 text-gray-700 hover:bg-gray-200 rounded-lg transition">Abbrechen</button>
                                        <button type="submit" id="submitCommentBtn" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                            <i class="fas fa-paper-plane mr-2 text-white"></i>Posten
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div id="commentsList" class="space-y-4 text-white">
                            @forelse($idea->comments as $comment)
                                <div class="comment-item flex space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition" data-comment-id="{{ $comment->id }}">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0
                                        {{ $comment->user->role === 'admin' ? 'bg-red-500' : 'bg-blue-500' }}">
                                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 text-white">
                                        <div class="flex items-center justify-between mb-2 text-white">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $comment->user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                            </div>
                                            @if($comment->user_id === $user->id || $user->isAdmin())
                                                <button onclick="deleteComment({{ $comment->id }})" class="text-red-600 hover:text-red-800 text-sm" title="Kommentar löschen">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <p class="text-gray-700 whitespace-pre-line">{{ $comment->comment }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12" id="emptyState">
                                    <i class="fas fa-comments text-gray-300 text-5xl mb-4"></i>
                                    <p class="text-gray-600 mb-2">Noch keine Kommentare</p>
                                    <p class="text-sm text-gray-500">Seien Sie der Erste, der seine Gedanken teilt!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-users text-indigo-600 mr-2"></i>Team
                    </h3>
                    <div class="flex items-center space-x-3 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg text-white">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white font-bold text-xl" 
                             style="background: {{ $idea->team->color }}">
                            {{ strtoupper(substr($idea->team->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $idea->team->name }}</p>
                            <p class="text-sm text-gray-600">{{ $idea->team->member_count }} Mitglieder</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 text-white">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-chart-bar text-indigo-600 mr-2"></i>Voting-Statistik
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-arrow-up text-green-600 text-xl mr-3 text-white"></i>
                                <span class="text-sm text-gray-700">Upvotes</span>
                            </div>
                            <span class="text-2xl font-bold text-green-600" id="voteCount2">{{ $idea->votes }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center text-white">
                                <i class="fas fa-users text-gray-600 text-xl mr-3 text-white"></i>
                                <span class="text-sm text-gray-700">Voter</span>
                            </div>
                            <span class="text-2xl font-bold text-gray-600" id="votersCount">{{ $idea->votes()->count() }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 text-white">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-history text-indigo-600 mr-2 text-white"></i>Status-Verlauf
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-gray-100 {{ $idea->status === 'pending' ? 'bg-yellow-100' : '' }}">
                                <i class="fas fa-clock text-sm {{ $idea->status === 'pending' ? 'text-yellow-600' : 'text-gray-400' }}"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Offen</p>
                                <p class="text-xs text-gray-500">Wartet auf Prüfung</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-gray-100 {{ $idea->status === 'in-review' ? 'bg-blue-100' : '' }}">
                                <i class="fas fa-eye text-sm {{ $idea->status === 'in-review' ? 'text-blue-600' : 'text-gray-400' }}"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">In Prüfung</p>
                                <p class="text-xs text-gray-500">Wird aktuell evaluiert</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-gray-100 {{ $idea->status === 'approved' ? 'bg-green-100' : '' }}">
                                <i class="fas fa-check-circle text-sm {{ $idea->status === 'approved' ? 'text-green-600' : 'text-gray-400' }}"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Genehmigt</p>
                                <p class="text-xs text-gray-500">Bereit für die Umsetzung</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white text-white">
                    <h3 class="text-lg font-bold mb-3">Idee teilen</h3>
                    <p class="text-sm text-indigo-100 mb-4 text-white">Mobilisieren Sie Unterstützung für Ihren Vorschlag!</p>
                    <div class="flex space-x-2 text-white">
                        <button onclick="copyIdeaLink()" class="flex-1 px-3 py-2 bg-white text-indigo-600 rounded-lg hover:bg-indigo-50 transition text-sm font-semibold">
                            <i class="fas fa-link mr-1 text-white"></i>Link kopieren
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="voteToast" class="hidden fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <i class="fas fa-check-circle mr-2 text-white"></i>
        <span>Vote gespeichert!</span>
    </div>

    <script>
        // Vote Logik
        function toggleVote() {
            const button = document.getElementById('voteButton');
            const ideaId = button.dataset.ideaId;
            const csrfToken = '{{ csrf_token() }}';
            
            button.disabled = true;
            button.style.opacity = '0.6';
            
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
                    document.getElementById('voteCount').textContent = data.voteCount;
                    document.getElementById('voteCount2').textContent = data.voteCount;
                    document.getElementById('voteText').textContent = data.hasVoted ? 'Gevotet' : 'Voten';
                    
                    if (data.hasVoted) {
                        button.classList.replace('bg-indigo-600', 'bg-green-600');
                    } else {
                        button.classList.replace('bg-green-600', 'bg-indigo-600');
                    }
                    showToast(data.hasVoted ? 'Stimme abgegeben!' : 'Stimme entfernt!');
                }
            })
            .finally(() => {
                button.disabled = false;
                button.style.opacity = '1';
            });
        }

        // Kommentar Logik
        function submitComment(event) {
            event.preventDefault();
            const input = document.getElementById('commentInput');
            const btn = document.getElementById('submitCommentBtn');
            const comment = input.value.trim();

            if (!comment) return;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2 text-white"></i>Sendet...';

            fetch('/tenant/{{ $tenant->id }}/ideas/{{ $idea->id }}/comments', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ comment: comment })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Einfachste Lösung zum Aktualisieren der Liste
                }
            });
        }

        function deleteComment(id) {
            if (!confirm('Diesen Kommentar wirklich löschen?')) return;
            fetch(`/tenant/{{ $tenant->id }}/ideas/{{ $idea->id }}/comments/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(() => location.reload());
        }

        function copyIdeaLink() {
            navigator.clipboard.writeText(window.location.href);
            showToast('Link kopiert!');
        }

        function showToast(msg) {
            const toast = document.getElementById('voteToast');
            toast.querySelector('span').textContent = msg;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3000);
        }

        // Zeichenzähler
        document.getElementById('commentInput')?.addEventListener('input', function() {
            document.getElementById('charCount').textContent = this.value.length;
        });
    </script>
</body>
</html>