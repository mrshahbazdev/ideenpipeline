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

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm animate-pulse">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
                    <div>
                        <p class="text-green-800 font-semibold">{{ session('success') }}</p>
                        <p class="text-green-600 text-sm">Status has been updated successfully.</p>
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
                                    id="voteButton"
                                    data-idea-id="{{ $idea->id }}"
                                    data-has-voted="{{ $idea->hasVoted($user) ? 'true' : 'false' }}"
                                    class="vote-button flex flex-col items-center px-6 py-3 rounded-lg transition shadow-lg font-semibold transform hover:scale-105
                                    {{ $idea->hasVoted($user) ? 'bg-green-600 hover:bg-green-700' : 'bg-indigo-600 hover:bg-indigo-700' }} text-white"
                                    onclick="toggleVote()">
                                    <i class="fas fa-arrow-up text-2xl mb-1"></i>
                                    <span class="font-bold text-lg" id="voteCount">{{ $idea->votes }}</span>
                                    <span class="text-xs" id="voteText">{{ $idea->hasVoted($user) ? 'Voted' : 'Vote' }}</span>
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

                    <!-- Actions (if owner or admin or has permissions) -->
                    <!-- Actions (if owner or admin or has permissions) -->
                <div class="px-8 pb-8 border-t border-gray-200 pt-6">
                    <div class="flex flex-wrap items-center gap-3">
                        
                        <!-- Edit Button (All roles with permissions) -->
                        @if($idea->canEditBasic($user) || $idea->canEditDeveloper($user) || $idea->canEditWorkBee($user))
                            <a href="{{ route('tenant.ideas.edit', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" 
                            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold shadow-lg">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Idea
                                @if($user->isDeveloper())
                                    <span class="text-xs opacity-80">(Developer)</span>
                                @elseif($user->isWorkBee())
                                    <span class="text-xs opacity-80">(Work-Bee)</span>
                                @elseif($user->isAdmin())
                                    <span class="text-xs opacity-80">(All Fields)</span>
                                @endif
                            </a>
                        @endif

                        <!-- Admin Quick Actions -->
                        @if($user->isAdmin())
                            <div class="flex items-center space-x-3 ml-auto">
                                <!-- Approve Button -->
                                @if($idea->status !== 'approved')
                                    <form method="POST" action="{{ route('tenant.ideas.update-status', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" 
                                                class="px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold shadow-lg transform hover:scale-105"
                                                onclick="return confirm('Approve this idea?')">
                                            <i class="fas fa-check mr-2"></i>Approve
                                        </button>
                                    </form>
                                @else
                                    <span class="px-4 py-3 bg-green-100 text-green-800 rounded-lg font-semibold">
                                        <i class="fas fa-check-circle mr-2"></i>Approved
                                    </span>
                                @endif

                                <!-- Review Button -->
                                @if($idea->status !== 'in-review')
                                    <form method="POST" action="{{ route('tenant.ideas.update-status', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="in-review">
                                        <button type="submit" 
                                                class="px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold shadow-lg transform hover:scale-105">
                                            <i class="fas fa-eye mr-2"></i>Review
                                        </button>
                                    </form>
                                @else
                                    <span class="px-4 py-3 bg-blue-100 text-blue-800 rounded-lg font-semibold">
                                        <i class="fas fa-eye mr-2"></i>In Review
                                    </span>
                                @endif

                                <!-- Reject Button -->
                                @if($idea->status !== 'rejected')
                                    <form method="POST" action="{{ route('tenant.ideas.update-status', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" 
                                                class="px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold shadow-lg transform hover:scale-105"
                                                onclick="return confirm('Reject this idea?')">
                                            <i class="fas fa-times mr-2"></i>Reject
                                        </button>
                                    </form>
                                @else
                                    <span class="px-4 py-3 bg-red-100 text-red-800 rounded-lg font-semibold">
                                        <i class="fas fa-times-circle mr-2"></i>Rejected
                                    </span>
                                @endif

                                <!-- Implement Button -->
                                @if($idea->status === 'approved' && !$idea->in_implementation)
                                    <form method="POST" action="{{ route('tenant.ideas.update-status', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="implemented">
                                        <button type="submit" 
                                                class="px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold shadow-lg transform hover:scale-105"
                                                onclick="return confirm('Mark as implemented?')">
                                            <i class="fas fa-rocket mr-2"></i>Implement
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>

                <!-- Comments Section -->

                </div>

                <!-- Comments Section -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center justify-between">
                            <span>
                                <i class="fas fa-comments text-indigo-600 mr-2"></i>
                                Discussion (<span id="commentsCount">{{ $idea->comments()->count() }}</span>)
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
                            <form id="commentForm" onsubmit="submitComment(event)">
                                <textarea 
                                    id="commentInput"
                                    rows="3"
                                    placeholder="Share your thoughts on this idea..."
                                    maxlength="1000"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent mb-3"
                                ></textarea>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">
                                        <span id="charCount">0</span> / 1000 characters
                                    </span>
                                    <div class="flex space-x-2">
                                        <button 
                                            type="button"
                                            @click="commentOpen = false"
                                            class="px-4 py-2 text-gray-700 hover:bg-gray-200 rounded-lg transition">
                                            Cancel
                                        </button>
                                        <button 
                                            type="submit"
                                            id="submitCommentBtn"
                                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                            <i class="fas fa-paper-plane mr-2"></i>Post Comment
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Comments List -->
                        <div id="commentsList" class="space-y-4">
                            @forelse($idea->comments as $comment)
                                <div class="comment-item flex space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition" data-comment-id="{{ $comment->id }}">
                                    <!-- User Avatar -->
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0
                                        {{ $comment->user->role === 'admin' ? 'bg-red-500' : '' }}
                                        {{ $comment->user->role === 'developer' ? 'bg-purple-500' : '' }}
                                        {{ $comment->user->role === 'work-bee' ? 'bg-green-500' : '' }}
                                        {{ $comment->user->role === 'standard' ? 'bg-blue-500' : '' }}
                                    ">
                                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                    </div>

                                    <!-- Comment Content -->
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $comment->user->name }}</p>
                                                <p class="text-xs text-gray-500" title="{{ $comment->created_at->format('M d, Y \a\t g:i A') }}">
                                                    {{ $comment->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            
                                            @if($comment->user_id === $user->id || $user->isAdmin())
                                                <button 
                                                    onclick="deleteComment({{ $comment->id }})"
                                                    class="text-red-600 hover:text-red-800 text-sm"
                                                    title="Delete comment">
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
                                    <p class="text-gray-600 mb-2">No comments yet</p>
                                    <p class="text-sm text-gray-500">Be the first to share your thoughts!</p>
                                </div>
                            @endforelse
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
                            <span class="text-2xl font-bold text-green-600" id="voteCount2">{{ $idea->votes }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-users text-gray-600 text-xl mr-3"></i>
                                <span class="text-sm text-gray-700">Voters</span>
                            </div>
                            <span class="text-2xl font-bold text-gray-600" id="votersCount">{{ $idea->votes()->count() }}</span>
                        </div>

                        @if($idea->votes()->count() > 0)
                            <div class="pt-3 border-t border-gray-200">
                                <p class="text-xs text-gray-600 mb-2">Voting Rate</p>
                                @php
                                    $teamMemberCount = $idea->team->member_count;
                                    $votePercentage = $teamMemberCount > 0 ? round(($idea->votes()->count() / $teamMemberCount) * 100) : 0;
                                @endphp
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $votePercentage }}%"></div>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">{{ $votePercentage }}% of team members voted</p>
                            </div>
                        @endif
                    </div>
                </div>


                <!-- Recent Voters -->
                <!-- Recent Supporters -->
                @if($idea->votes()->count() > 0)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-heart text-red-500 mr-2"></i>
                            Recent Supporters ({{ $idea->votes()->count() }})
                        </h3>
                        <div class="space-y-3">
                            @foreach($idea->votes()->with('user')->latest()->take(10)->get() as $vote)
                                <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg transition">
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
                                    </div>
                                    <i class="fas fa-thumbs-up text-green-500"></i>
                                </div>
                            @endforeach
                        </div>

                        @if($idea->votes()->count() > 10)
                            <div class="mt-4 text-center">
                                <p class="text-sm text-gray-600">
                                    + {{ $idea->votes()->count() - 10 }} more supporters
                                </p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-heart text-gray-300 mr-2"></i>
                            Supporters
                        </h3>
                        <div class="text-center py-6">
                            <i class="fas fa-users text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-600 text-sm">No votes yet</p>
                            <p class="text-gray-500 text-xs mt-1">Be the first to support this idea!</p>
                        </div>
                    </div>
                @endif

                <!-- Status History -->
<div class="bg-white rounded-xl shadow-lg p-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">
        <i class="fas fa-history text-indigo-600 mr-2"></i>
        Status History
    </h3>
    <div class="space-y-3">
        <div class="flex items-start space-x-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                {{ $idea->status === 'pending' ? 'bg-yellow-100' : 'bg-gray-100' }}">
                <i class="fas fa-clock text-sm
                    {{ $idea->status === 'pending' ? 'text-yellow-600' : 'text-gray-400' }}"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900">Pending</p>
                <p class="text-xs text-gray-500">Awaiting review</p>
            </div>
            @if($idea->status === 'pending')
                <i class="fas fa-check text-green-500"></i>
            @endif
        </div>

        <div class="flex items-start space-x-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                {{ $idea->status === 'in-review' ? 'bg-blue-100' : 'bg-gray-100' }}">
                <i class="fas fa-eye text-sm
                    {{ $idea->status === 'in-review' ? 'text-blue-600' : 'text-gray-400' }}"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900">In Review</p>
                <p class="text-xs text-gray-500">Being evaluated</p>
            </div>
            @if($idea->status === 'in-review')
                <i class="fas fa-check text-green-500"></i>
            @endif
        </div>

        <div class="flex items-start space-x-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                {{ $idea->status === 'approved' ? 'bg-green-100' : 'bg-gray-100' }}">
                <i class="fas fa-check-circle text-sm
                    {{ $idea->status === 'approved' ? 'text-green-600' : 'text-gray-400' }}"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900">Approved</p>
                <p class="text-xs text-gray-500">Ready for implementation</p>
            </div>
            @if($idea->status === 'approved')
                <i class="fas fa-check text-green-500"></i>
            @endif
        </div>

        @if($idea->status === 'rejected')
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 bg-red-100">
                    <i class="fas fa-times-circle text-sm text-red-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">Rejected</p>
                    <p class="text-xs text-gray-500">Not moving forward</p>
                </div>
                <i class="fas fa-check text-red-500"></i>
            </div>
        @endif

        @if($idea->status === 'implemented')
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 bg-purple-100">
                    <i class="fas fa-rocket text-sm text-purple-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">Implemented</p>
                    <p class="text-xs text-gray-500">Live in production!</p>
                </div>
                <i class="fas fa-check text-purple-500"></i>
            </div>
        @endif
    </div>
</div>

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
    <!-- Vote Toast -->
    <div id="voteToast" class="hidden fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-bounce">
        <i class="fas fa-check-circle mr-2"></i>
        <span>Vote recorded!</span>
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
    <!-- Vote JavaScript -->
    <script>
        let hasVoted = {{ $idea->hasVoted($user) ? 'true' : 'false' }};
        
        function toggleVote() {
            const button = document.getElementById('voteButton');
            const ideaId = button.dataset.ideaId;
            const csrfToken = '{{ csrf_token() }}';
            
            // Disable button during request
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
                    // Update vote count
                    document.getElementById('voteCount').textContent = data.voteCount;
                    document.getElementById('voteText').textContent = data.hasVoted ? 'Voted' : 'Vote';
                    
                    // Update button state
                    hasVoted = data.hasVoted;
                    button.dataset.hasVoted = data.hasVoted;
                    
                    // Update button colors
                    if (data.hasVoted) {
                        button.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                        button.classList.add('bg-green-600', 'hover:bg-green-700');
                    } else {
                        button.classList.remove('bg-green-600', 'hover:bg-green-700');
                        button.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                    }
                    
                    // Update voters count in sidebar
                    const votersCountEl = document.getElementById('votersCount');
                    if (votersCountEl) {
                        votersCountEl.textContent = data.votersCount;
                    }
                    
                    // Show toast
                    showToast(data.message);
                    
                    // Reload voters list if visible
                    if (data.hasVoted) {
                        setTimeout(() => location.reload(), 1000);
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Vote error:', error);
                alert('Failed to process vote. Please try again.');
            })
            .finally(() => {
                // Re-enable button
                button.disabled = false;
                button.style.opacity = '1';
            });
        }
        
        function showToast(message) {
            const toast = document.getElementById('voteToast');
            if (toast) {
                toast.querySelector('span').textContent = message;
                toast.classList.remove('hidden');
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 3000);
            }
        }
    </script>
    <!-- Comments JavaScript -->
    <script>
        // Character counter
        const commentInput = document.getElementById('commentInput');
        const charCount = document.getElementById('charCount');
        
        if (commentInput) {
            commentInput.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
        }

        // Submit comment
        function submitComment(event) {
            event.preventDefault();
            
            const form = document.getElementById('commentForm');
            const input = document.getElementById('commentInput');
            const submitBtn = document.getElementById('submitCommentBtn');
            const comment = input.value.trim();
            
            if (!comment) {
                alert('Please enter a comment');
                return;
            }
            
            // Disable button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Posting...';
            
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
                    // Hide empty state if exists
                    const emptyState = document.getElementById('emptyState');
                    if (emptyState) {
                        emptyState.remove();
                    }
                    
                    // Add new comment to list
                    const commentsList = document.getElementById('commentsList');
                    const newComment = createCommentElement(data.comment);
                    commentsList.insertAdjacentHTML('afterbegin', newComment);
                    
                    // Update count
                    document.getElementById('commentsCount').textContent = data.commentsCount;
                    
                    // Reset form
                    input.value = '';
                    charCount.textContent = '0';
                    
                    // Close form
                    Alpine.store('commentOpen', false);
                    
                    // Show success
                    showToast('Comment added successfully!');
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Comment error:', error);
                alert('Failed to add comment');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Post Comment';
            });
        }
        
        // Create comment HTML element
        function createCommentElement(comment) {
            const roleColors = {
                'admin': 'bg-red-500',
                'developer': 'bg-purple-500',
                'work-bee': 'bg-green-500',
                'standard': 'bg-blue-500'
            };
            
            const bgColor = roleColors[comment.user.role] || 'bg-blue-500';
            
            return `
                <div class="comment-item flex space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition animate-fadeIn" data-comment-id="${comment.id}">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0 ${bgColor}">
                        ${comment.user.avatar}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="font-semibold text-gray-900">${comment.user.name}</p>
                                <p class="text-xs text-gray-500" title="${comment.created_at_full}">
                                    ${comment.created_at}
                                </p>
                            </div>
                            <button 
                                onclick="deleteComment(${comment.id})"
                                class="text-red-600 hover:text-red-800 text-sm"
                                title="Delete comment">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <p class="text-gray-700 whitespace-pre-line">${comment.comment}</p>
                    </div>
                </div>
            `;
        }
        
        // Delete comment
        function deleteComment(commentId) {
            if (!confirm('Delete this comment?')) {
                return;
            }
            
            fetch(`/tenant/{{ $tenant->id }}/ideas/{{ $idea->id }}/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove comment from DOM
                    const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
                    if (commentElement) {
                        commentElement.style.transition = 'opacity 0.3s';
                        commentElement.style.opacity = '0';
                        setTimeout(() => {
                            commentElement.remove();
                            
                            // Update count
                            document.getElementById('commentsCount').textContent = data.commentsCount;
                            
                            // Show empty state if no comments
                            if (data.commentsCount === 0) {
                                const commentsList = document.getElementById('commentsList');
                                commentsList.innerHTML = `
                                    <div class="text-center py-12" id="emptyState">
                                        <i class="fas fa-comments text-gray-300 text-5xl mb-4"></i>
                                        <p class="text-gray-600 mb-2">No comments yet</p>
                                        <p class="text-sm text-gray-500">Be the first to share your thoughts!</p>
                                    </div>
                                `;
                            }
                        }, 300);
                    }
                    
                    showToast('Comment deleted');
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                alert('Failed to delete comment');
            });
        }
        
        function showToast(message) {
            const toast = document.getElementById('voteToast');
            if (toast) {
                toast.querySelector('span').textContent = message;
                toast.classList.remove('hidden');
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 3000);
            }
        }
    </script>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
    </style>

</body>
</html>
