<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ideas Management - {{ $currentTeam->name }} - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .table-row:hover {
            background-color: #F9FAFB;
        }
        .sortable {
            cursor: pointer;
            user-select: none;
        }
        .sortable:hover {
            background-color: #F3F4F6;
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ mobileMenuOpen: false }">

    @include('tenant.partials.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-table text-indigo-600 mr-3"></i>
                        Ideas Management
                    </h1>
                    <p class="text-gray-600 mt-2">Structured view of all team ideas</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('tenant.ideas.index', ['tenantId' => $tenant->id]) }}" 
                       class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-th-large mr-2"></i>Card View
                    </a>
                    <a href="{{ route('tenant.ideas.create', ['tenantId' => $tenant->id]) }}" 
                       class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg">
                        <i class="fas fa-plus mr-2"></i>New Idea
                    </a>
                </div>
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
        <div class="grid md:grid-cols-6 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-xs text-gray-600 mb-1">Total Ideas</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_ideas'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-xs text-gray-600 mb-1">Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-xs text-gray-600 mb-1">Approved</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-xs text-gray-600 mb-1">In Progress</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['in_implementation'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-xs text-gray-600 mb-1">Avg Pain</p>
                <p class="text-2xl font-bold text-orange-600">{{ $stats['avg_pain'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-xs text-gray-600 mb-1">Total Cost</p>
                <p class="text-2xl font-bold text-purple-600">${{ number_format($stats['total_cost'], 0) }}</p>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="sortable px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                onclick="window.location.href='{{ route('tenant.ideas.table', ['tenantId' => $tenant->id, 'sort' => 'problem_short', 'order' => $sortBy === 'problem_short' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}'">
                                <div class="flex items-center space-x-2">
                                    <span>Idea / Problem</span>
                                    @if($sortBy === 'problem_short')
                                        <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} text-indigo-600"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </div>
                            </th>
                            <th class="sortable px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                onclick="window.location.href='{{ route('tenant.ideas.table', ['tenantId' => $tenant->id, 'sort' => 'status', 'order' => $sortBy === 'status' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}'">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>Status</span>
                                    @if($sortBy === 'status')
                                        <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} text-indigo-600"></i>
                                    @endif
                                </div>
                            </th>
                            <th class="sortable px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                onclick="window.location.href='{{ route('tenant.ideas.table', ['tenantId' => $tenant->id, 'sort' => 'pain_score', 'order' => $sortBy === 'pain_score' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}'">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>Schmerz</span>
                                    @if($sortBy === 'pain_score')
                                        <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} text-indigo-600"></i>
                                    @endif
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Lösung
                            </th>
                            <th class="sortable px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                onclick="window.location.href='{{ route('tenant.ideas.table', ['tenantId' => $tenant->id, 'sort' => 'cost_estimate', 'order' => $sortBy === 'cost_estimate' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}'">
                                <div class="flex items-center justify-end space-x-2">
                                    <span>Kosten</span>
                                    @if($sortBy === 'cost_estimate')
                                        <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} text-indigo-600"></i>
                                    @endif
                                </div>
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Dauer
                            </th>
                            <th class="sortable px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                onclick="window.location.href='{{ route('tenant.ideas.table', ['tenantId' => $tenant->id, 'sort' => 'priority_1', 'order' => $sortBy === 'priority_1' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}'">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>Prio 1</span>
                                    @if($sortBy === 'priority_1')
                                        <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} text-indigo-600"></i>
                                    @endif
                                </div>
                            </th>
                            <th class="sortable px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                onclick="window.location.href='{{ route('tenant.ideas.table', ['tenantId' => $tenant->id, 'sort' => 'priority_2', 'order' => $sortBy === 'priority_2' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}'">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>Prio 2</span>
                                    @if($sortBy === 'priority_2')
                                        <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} text-indigo-600"></i>
                                    @endif
                                </div>
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Umsetzung
                            </th>
                            <th class="sortable px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                onclick="window.location.href='{{ route('tenant.ideas.table', ['tenantId' => $tenant->id, 'sort' => 'votes', 'order' => $sortBy === 'votes' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}'">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>Votes</span>
                                    @if($sortBy === 'votes')
                                        <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} text-indigo-600"></i>
                                    @endif
                                </div>
                            </th>

                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($ideas as $idea)
                            <tr class="table-row">
                                <!-- Idea/Problem -->
                                <td class="px-4 py-4">
                                    <div class="max-w-xs">
                                        <p class="font-semibold text-gray-900 text-sm">{{ $idea->problem_short }}</p>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $idea->goal }}</p>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $idea->getStatusBadgeClass() }}">
                                        {{ ucfirst($idea->status) }}
                                    </span>
                                </td>

                                <!-- Pain Score -->
                                <td class="px-4 py-4 text-center">
                                    <div class="flex items-center justify-center">
                                        <span class="text-2xl font-bold {{ $idea->getPainColor() }}">
                                            {{ $idea->pain_score }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Solution -->
                                <td class="px-4 py-4">
                                    <div class="max-w-xs">
                                        @if($idea->solution)
                                            <p class="text-xs text-gray-700 line-clamp-2">{{ $idea->solution }}</p>
                                        @else
                                            <span class="text-xs text-gray-400 italic">No solution provided</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Cost -->
                                <td class="px-4 py-4 text-right">
                                    @if($idea->cost_estimate)
                                        <span class="font-semibold text-gray-900">
                                            ${{ number_format($idea->cost_estimate, 2) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                <!-- Duration -->
                                <td class="px-4 py-4 text-center">
                                    @if($idea->duration_estimate)
                                        <span class="text-sm text-gray-700">{{ $idea->duration_estimate }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                <!-- Priority 1 -->
                                <td class="px-4 py-4 text-center">
                                    <span class="text-lg font-bold text-indigo-600">
                                        {{ number_format($idea->priority_1, 2) }}
                                    </span>
                                </td>

                                <!-- Priority 2 -->
                                <td class="px-4 py-4 text-center">
                                    <span class="text-lg font-bold text-purple-600">
                                        {{ number_format($idea->priority_2, 2) }}
                                    </span>
                                </td>

                                <!-- Implementation -->
                                <td class="px-4 py-4 text-center">
                                    @if($idea->in_implementation)
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-check-circle text-green-500 text-xl mb-1"></i>
                                            @if($idea->implementation_date)
                                                <span class="text-xs text-gray-600">{{ $idea->implementation_date->format('M d, Y') }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <!-- Votes -->
                                <td class="px-4 py-4 text-center">
                                    <button onclick="quickVote({{ $idea->id }}, this)" 
                                            data-has-voted="{{ $idea->hasVoted($user) ? 'true' : 'false' }}"
                                            class="inline-flex items-center px-3 py-2 rounded-lg transition
                                            {{ $idea->hasVoted($user) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }} hover:bg-indigo-100">
                                        <i class="fas fa-arrow-up mr-2"></i>
                                        <span class="font-bold">{{ $idea->votes }}</span>
                                    </button>
                                </td>

                                                            <!-- Actions -->
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- View -->
                                    <a href="{{ route('tenant.ideas.show', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" 
                                    class="text-indigo-600 hover:text-indigo-900 text-lg" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <!-- Edit -->
                                    @if($idea->canEditBasic($user) || $idea->canEditDeveloper($user) || $idea->canEditWorkBee($user))
                                        <a href="{{ route('tenant.ideas.edit', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" 
                                        class="text-gray-600 hover:text-gray-900 text-lg" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    
                                    @if($user->isAdmin())
                                        <!-- Quick Approve -->
                                        @if($idea->status !== 'approved')
                                            <form method="POST" action="{{ route('tenant.ideas.update-status', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="text-green-600 hover:text-green-900 text-lg" 
                                                        title="Approve"
                                                        onclick="return confirm('Approve this idea?')">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Quick Review -->
                                        @if($idea->status !== 'in-review')
                                            <form method="POST" action="{{ route('tenant.ideas.update-status', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="in-review">
                                                <button type="submit" class="text-blue-600 hover:text-blue-900 text-lg" 
                                                        title="Move to Review">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Quick Reject -->
                                        @if($idea->status !== 'rejected')
                                            <form method="POST" action="{{ route('tenant.ideas.update-status', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-lg" 
                                                        title="Reject"
                                                        onclick="return confirm('Reject this idea?')">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>


                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-12 text-center">
                                    <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                                    <p class="text-gray-600 mb-2">No ideas yet</p>
                                    <a href="{{ route('tenant.ideas.create', ['tenantId' => $tenant->id]) }}" 
                                       class="text-indigo-600 hover:text-indigo-800 font-semibold">
                                        <i class="fas fa-plus mr-1"></i>Submit First Idea
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Legend -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h4 class="font-semibold text-blue-900 mb-3 text-sm flex items-center">
                <i class="fas fa-info-circle mr-2"></i>Priority Calculation Formula
            </h4>
            <div class="grid md:grid-cols-2 gap-6 text-sm text-blue-800">
                <div class="bg-white rounded-lg p-4">
                    <p class="font-bold text-indigo-600 mb-2">Prio 1 = (Kosten / 100) + Dauer</p>
                    <p class="text-xs text-gray-600">Lower is better - measures cost and time investment</p>
                    <div class="mt-2 text-xs">
                        <strong>Example:</strong> ($400 / 100) + 3 days = 7.00
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <p class="font-bold text-purple-600 mb-2">Prio 2 = Prio 1 / Schmerz</p>
                    <p class="text-xs text-gray-600">Lower is better - ROI considering pain level</p>
                    <div class="mt-2 text-xs">
                        <strong>Example:</strong> 7.00 / 8 = 0.88
                    </div>
                </div>
            </div>
            
            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded p-3">
                <p class="text-xs font-semibold text-yellow-900 mb-2">
                    <i class="fas fa-lightbulb mr-1"></i>Implementation Priority Strategy:
                </p>
                <ul class="text-xs text-yellow-800 space-y-1">
                    <li>• <strong>High Schmerz + Low Prio 2</strong> = Quick win! (High pain, cheap/fast fix)</li>
                    <li>• <strong>High Schmerz + High Prio 2</strong> = Important but expensive (plan carefully)</li>
                    <li>• <strong>Low Schmerz + Low Prio 2</strong> = Low priority (nice to have)</li>
                </ul>
            </div>
        </div>


    </div>
<script>
    function quickVote(ideaId, button) {
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
                // Update vote count
                button.querySelector('span').textContent = data.voteCount;
                
                // Update button style
                if (data.hasVoted) {
                    button.classList.remove('bg-gray-100', 'text-gray-700');
                    button.classList.add('bg-green-100', 'text-green-700');
                } else {
                    button.classList.remove('bg-green-100', 'text-green-700');
                    button.classList.add('bg-gray-100', 'text-gray-700');
                }
                
                button.dataset.hasVoted = data.hasVoted;
            }
        })
        .catch(error => {
            console.error('Vote error:', error);
            alert('Failed to vote');
        })
        .finally(() => {
            button.disabled = false;
            button.style.opacity = '1';
        });
    }
</script>

</body>
</html>
