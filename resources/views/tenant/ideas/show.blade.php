<!DOCTYPE html>
<html lang="en">
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
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('tenant.ideas.index', ['tenantId' => $tenant->id]) }}" 
               class="inline-flex items-center text-gray-600 hover:text-gray-900 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Ideas
            </a>
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

        <div class="grid lg:grid-cols-3 gap-8">
            
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Idea Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <!-- Header -->
                    <div class="p-8 border-b border-gray-200">
                        <!-- Status & Priority Badges -->
                        <div class="flex items-center space-x-2 mb-4">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $idea->getStatusBadgeClass() }}">
                                <i class="fas fa-circle mr-1 text-xs"></i>{{ ucfirst(str_replace('-', ' ', $idea->status)) }}
                            </span>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $idea->getPriorityBadgeClass() }}">
                                <i class="fas fa-flag mr-1"></i>{{ ucfirst($idea->priority) }} Priority
                            </span>
                        </div>

                        <!-- Title -->
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">
                            {{ $idea->title }}
                        </h1>

                        <!-- Meta Info -->
                        <div class="flex items-center justify-between">
                            <!-- Author -->
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
                                        {{ $idea->created_at->format('M d, Y \a\t g:i A') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Vote Button -->
                            <div class="flex items-center space-x-3">
                                <button 
                                    class="vote-button flex flex-col items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-lg
                                    {{ $idea->hasVoted($user) ? 'active bg-green-600 hover:bg-green-700' : '' }}"
                                    onclick="voteIdea({{ $idea->id }})">
                                    <i class="fas fa-arrow-up text-2xl mb-1"></i>
                                    <span class="font-bold text-lg">{{ $idea->votes }}</span>
                                    <span class="text-xs">{{ $idea->hasVoted($user) ? 'Voted' : 'Vote' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">
                            <i class="fas fa-align-left text-indigo-600 mr-2"></i>
                            Description
                        </h2>
                        <div class="prose max-w-none">
                            <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $idea->description }}</p>
                        </div>
                    </div>

                    <!-- Tags -->
                    @if($idea->tags && count($idea->tags) > 0)
                        <div class="px-8 pb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-3">
                                <i class="fas fa-tags text-indigo-600 mr-2"></i>
                                Tags
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

                    <!-- Actions (if owner or admin) -->
                    @if($user->id === $idea->user_id || $user->isAdmin())
                        <div class="px-8 pb-8 flex items-center space-x-3">
                            @if($user->id === $idea->user_id)
                                <a href="#" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                    <i class="fas fa-edit mr-2"></i>Edit Idea
                                </a>
                            @endif

                            @if($user->isAdmin())
                                <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    <i class="fas fa-check mr-2"></i>Approve
                                </button>
                                <button class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                                    <i class="fas fa-clock mr-2"></i>Review
                                </button>
                                <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                    <i class="fas fa-times mr-2"></i>Reject
                                </button>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Comments Section -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center justify-between">
                            <span>
                                <i class="fas fa-comments text-indigo-600 mr-2"></i>
                                Discussion (0)
                            </span>
                            <button 
                                @click="commentOpen = !commentOpen"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm">
                                <i class="fas fa-plus mr-2"></i>Add Comment
                            </button>
                        </h2>

                        <!-- Add Comment Form -->
                        <div x-show="commentOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200"
                             style="display: none;">
                            <textarea 
                                rows="3"
                                placeholder="Share your thoughts on this idea..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent mb-3"
                            ></textarea>
                            <div class="flex justify-end space-x-2">
                                <button 
                                    @click="commentOpen = false"
                                    class="px-4 py-2 text-gray-700 hover:bg-gray-200 rounded-lg transition">
                                    Cancel
                                </button>
                                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                    <i class="fas fa-paper-plane mr-2"></i>Post Comment
                                </button>
                            </div>
                        </div>

                        <!-- Comments List (Empty State) -->
                        <div class="text-center py-12">
                            <i class="fas fa-comments text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-600 mb-2">No comments yet</p>
                            <p class="text-sm text-gray-500">Be the first to share your thoughts!</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                
                <!-- Team Info -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-users text-indigo-600 mr-2"></i>
                        Team
                    </h3>
                    <div class="flex items-center space-x-3 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white font-bold text-xl" 
                             style="background: {{ $idea->team->color }}">
                            {{ strtoupper(substr($idea->team->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $idea->team->name }}</p>
                            <p class="text-sm text-gray-600">{{ $idea->team->member_count }} members</p>
                        </div>
                    </div>
                </div>

                <!-- Voting Stats -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-chart-bar text-indigo-600 mr-2"></i>
                        Voting Stats
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-arrow-up text-green-600 text-xl mr-3"></i>
                                <span class="text-sm text-gray-700">Upvotes</span>
                            </div>
                            <span class="text-2xl font-bold text-green-600">{{ $idea->votes }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-users text-gray-600 text-xl mr-3"></i>
                                <span class="text-sm text-gray-700">Voters</span>
                            </div>
                            <span class="text-2xl font-bold text-gray-600">{{ $idea->votes()->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Voters -->
                @if($idea->votes()->count() > 0)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-heart text-red-500 mr-2"></i>
                            Recent Supporters
                        </h3>
                        <div class="space-y-3">
                            @foreach($idea->votes()->with('user')->latest()->take(5)->get() as $vote)
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm
                                        {{ $vote->user->role === 'admin' ? 'bg-red-500' : '' }}
                                        {{ $vote->user->role === 'developer' ? 'bg-purple-500' : '' }}
                                        {{ $vote->user->role === 'work-bee' ? 'bg-green-500' : '' }}
                                        {{ $vote->user->role === 'standard' ? 'bg-blue-500' : '' }}
                                    ">
                                        {{ strtoupper(substr($vote->user->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $vote->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $vote->created_at->diffForHumans() }}</p>
                                    </div>
                                    <i class="fas fa-thumbs-up text-green-500"></i>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Idea Timeline -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-history text-indigo-600 mr-2"></i>
                        Timeline
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-plus text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Idea Created</p>
                                <p class="text-xs text-gray-500">{{ $idea->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>

                        @if($idea->created_at != $idea->updated_at)
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-edit text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Last Updated</p>
                                    <p class="text-xs text-gray-500">{{ $idea->updated_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Share Idea -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <h3 class="text-lg font-bold mb-3">
                        <i class="fas fa-share-alt mr-2"></i>
                        Share This Idea
                    </h3>
                    <p class="text-sm text-indigo-100 mb-4">Spread the word to get more support!</p>
                    <div class="flex space-x-2">
                        <button class="flex-1 px-3 py-2 bg-white text-indigo-600 rounded-lg hover:bg-indigo-50 transition text-sm font-semibold">
                            <i class="fas fa-link mr-1"></i>Copy Link
                        </button>
                        <button class="px-3 py-2 bg-white text-indigo-600 rounded-lg hover:bg-indigo-50 transition">
                            <i class="fas fa-envelope"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Vote Modal/Toast (Future) -->
    <div id="voteToast" class="hidden fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
        <i class="fas fa-check-circle mr-2"></i>Vote recorded!
    </div>

    <!-- JavaScript -->
    <script>
        function voteIdea(ideaId) {
            // Placeholder for vote functionality
            console.log('Voting for idea:', ideaId);
            
            // Show toast
            const toast = document.getElementById('voteToast');
            toast.classList.remove('hidden');
            
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
            
            // In real implementation, make AJAX call to vote endpoint
            // Then update UI accordingly
        }

        // Copy link functionality
        document.querySelector('.fa-link').parentElement.addEventListener('click', function() {
            navigator.clipboard.writeText(window.location.href);
            alert('Link copied to clipboard!');
        });
    </script>

</body>
</html>
