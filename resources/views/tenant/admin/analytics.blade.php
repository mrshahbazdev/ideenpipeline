<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50" x-data="{ mobileMenuOpen: false }">

    @include('tenant.partials.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-chart-line text-indigo-600 mr-3"></i>
                        Analytics Dashboard
                    </h1>
                    <p class="text-gray-600 mt-2">Comprehensive insights into your organization's performance</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Last Updated</p>
                    <p class="text-sm font-semibold text-gray-900">{{ now()->format('M d, Y g:i A') }}</p>
                </div>
            </div>

            <!-- Breadcrumb -->
            <div class="flex items-center space-x-2 text-sm text-gray-600">
                <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" class="hover:text-indigo-600">Dashboard</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900 font-medium">Analytics</span>
            </div>
        </div>

        <!-- Overview Stats -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-tachometer-alt text-indigo-600 mr-2"></i>
                Overview
            </h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Users -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <span class="text-sm font-semibold bg-white bg-opacity-20 px-3 py-1 rounded-full">
                            {{ $userStats['active'] }} Active
                        </span>
                    </div>
                    <p class="text-4xl font-bold mb-1">{{ $userStats['total'] }}</p>
                    <p class="text-blue-100 text-sm">Total Users</p>
                    <div class="mt-4 flex items-center text-sm">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>+{{ $userStats['new_this_month'] }} this month</span>
                    </div>
                </div>

                <!-- Total Teams -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-layer-group text-2xl"></i>
                        </div>
                        <span class="text-sm font-semibold bg-white bg-opacity-20 px-3 py-1 rounded-full">
                            {{ $teamStats['active'] }} Active
                        </span>
                    </div>
                    <p class="text-4xl font-bold mb-1">{{ $teamStats['total'] }}</p>
                    <p class="text-purple-100 text-sm">Total Teams</p>
                    <div class="mt-4 flex items-center text-sm">
                        <i class="fas fa-users mr-1"></i>
                        <span>Avg {{ $teamStats['avg_members'] }} members/team</span>
                    </div>
                </div>

                <!-- Total Ideas -->
                <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-lightbulb text-2xl"></i>
                        </div>
                        <span class="text-sm font-semibold bg-white bg-opacity-20 px-3 py-1 rounded-full">
                            {{ $ideaStats['approved'] }} Approved
                        </span>
                    </div>
                    <p class="text-4xl font-bold mb-1">{{ $ideaStats['total'] }}</p>
                    <p class="text-yellow-100 text-sm">Total Ideas</p>
                    <div class="mt-4 flex items-center text-sm">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>+{{ $ideaStats['this_month'] }} this month</span>
                    </div>
                </div>

                <!-- Engagement Score -->
                <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-fire text-2xl"></i>
                        </div>
                        <span class="text-sm font-semibold bg-white bg-opacity-20 px-3 py-1 rounded-full">
                            {{ $ideaStats['total_votes'] }} Votes
                        </span>
                    </div>
                    <p class="text-4xl font-bold mb-1">{{ $ideaStats['total_comments'] }}</p>
                    <p class="text-green-100 text-sm">Total Comments</p>
                    <div class="mt-4 flex items-center text-sm">
                        <i class="fas fa-chart-line mr-1"></i>
                        <span>High engagement</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid lg:grid-cols-2 gap-8 mb-8">
            <!-- User Distribution by Role -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-user-tag text-indigo-600 mr-2"></i>
                    Users by Role
                </h3>
                <div class="h-64">
                    <canvas id="roleChart"></canvas>
                </div>
                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-red-50 rounded-lg">
                        <p class="text-2xl font-bold text-red-600">{{ $userStats['by_role']['admin'] ?? 0 }}</p>
                        <p class="text-xs text-gray-600">Admins</p>
                    </div>
                    <div class="text-center p-3 bg-purple-50 rounded-lg">
                        <p class="text-2xl font-bold text-purple-600">{{ $userStats['by_role']['developer'] ?? 0 }}</p>
                        <p class="text-xs text-gray-600">Developers</p>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600">{{ $userStats['by_role']['work-bee'] ?? 0 }}</p>
                        <p class="text-xs text-gray-600">Work-Bees</p>
                    </div>
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600">{{ $userStats['by_role']['standard'] ?? 0 }}</p>
                        <p class="text-xs text-gray-600">Standard</p>
                    </div>
                </div>
            </div>

            <!-- Idea Status Distribution -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-tasks text-indigo-600 mr-2"></i>
                    Ideas by Status
                </h3>
                <div class="h-64">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-yellow-50 rounded-lg">
                        <p class="text-2xl font-bold text-yellow-600">{{ $ideaStats['pending'] }}</p>
                        <p class="text-xs text-gray-600">Pending</p>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600">{{ $ideaStats['approved'] }}</p>
                        <p class="text-xs text-gray-600">Approved</p>
                    </div>
                    <div class="text-center p-3 bg-purple-50 rounded-lg">
                        <p class="text-2xl font-bold text-purple-600">{{ $ideaStats['implemented'] }}</p>
                        <p class="text-xs text-gray-600">Implemented</p>
                    </div>
                    <div class="text-center p-3 bg-orange-50 rounded-lg">
                        <p class="text-2xl font-bold text-orange-600">{{ $ideaStats['avg_pain_score'] }}</p>
                        <p class="text-xs text-gray-600">Avg Pain Score</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Trends -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-chart-area text-indigo-600 mr-2"></i>
                Activity Trends (Last 30 Days)
            </h3>
            <div class="h-80">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <!-- Top Ideas & Contributors -->
        <div class="grid lg:grid-cols-2 gap-8 mb-8">
            <!-- Top Ideas -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                    Top Ideas (Most Votes)
                </h3>
                <div class="space-y-4">
                    @forelse($topIdeas as $index => $idea)
                        <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold shadow-lg flex-shrink-0">
                                #{{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 truncate">{{ $idea->problem_short }}</p>
                                <div class="flex items-center space-x-3 mt-2 text-xs text-gray-600">
                                    <span>
                                        <i class="fas fa-user mr-1"></i>{{ $idea->user->name }}
                                    </span>
                                    <span>
                                        <i class="fas fa-users mr-1"></i>{{ $idea->team->name }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 flex-shrink-0">
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-green-600">{{ $idea->votes }}</p>
                                    <p class="text-xs text-gray-600">votes</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-600">No ideas yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Top Contributors -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-star text-indigo-600 mr-2"></i>
                    Top Contributors
                </h3>
                <div class="space-y-4">
                    @forelse($topContributors as $index => $contributor)
                        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $contributor->role_color }} flex items-center justify-center text-white font-bold shadow-lg flex-shrink-0">
                                {{ $contributor->initials }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900">{{ $contributor->name }}</p>
                                <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded {{ $contributor->role_badge }}">
                                    {{ strtoupper(str_replace('-', ' ', $contributor->role)) }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-4 text-sm flex-shrink-0">
                                <div class="text-center">
                                    <p class="text-xl font-bold text-yellow-600">{{ $contributor->ideas_count }}</p>
                                    <p class="text-xs text-gray-600">ideas</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xl font-bold text-green-600">{{ $contributor->votes_count }}</p>
                                    <p class="text-xs text-gray-600">votes</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xl font-bold text-blue-600">{{ $contributor->comments_count }}</p>
                                    <p class="text-xs text-gray-600">comments</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-users text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-600">No contributors yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Team Performance -->
        @if($teamStats['largest_team'])
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-8 text-white mb-8">
                <h3 class="text-2xl font-bold mb-6 flex items-center">
                    <i class="fas fa-award mr-3"></i>
                    Team Spotlight
                </h3>
                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-indigo-100 text-sm mb-2">Largest Team</p>
                        <p class="text-3xl font-bold">{{ $teamStats['largest_team']->name }}</p>
                        <p class="text-indigo-200 text-sm mt-2">{{ $teamStats['largest_team']->member_count }} members</p>
                    </div>
                    <div>
                        <p class="text-indigo-100 text-sm mb-2">Total Ideas from Team</p>
                        <p class="text-3xl font-bold">{{ $teamStats['largest_team']->ideas()->count() }}</p>
                    </div>
                    <div>
                        <p class="text-indigo-100 text-sm mb-2">Team Status</p>
                        <span class="inline-block px-4 py-2 bg-white bg-opacity-20 rounded-full text-sm font-semibold">
                            <i class="fas fa-check-circle mr-1"></i>Active
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Stats Grid -->
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Ideas per User</p>
                        <p class="text-3xl font-bold text-gray-900">
                            {{ $userStats['total'] > 0 ? round($ideaStats['total'] / $userStats['total'], 1) : 0 }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-lightbulb text-indigo-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Votes per Idea</p>
                        <p class="text-3xl font-bold text-gray-900">
                            {{ $ideaStats['total'] > 0 ? round($ideaStats['total_votes'] / $ideaStats['total'], 1) : 0 }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Implementation Rate</p>
                        <p class="text-3xl font-bold text-gray-900">
                            {{ $ideaStats['total'] > 0 ? round(($ideaStats['implemented'] / $ideaStats['total']) * 100) : 0 }}%
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-rocket text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart.js Scripts -->
    <script>
        // Role Distribution Chart
        const roleCtx = document.getElementById('roleChart').getContext('2d');
        new Chart(roleCtx, {
            type: 'doughnut',
            data: {
                labels: ['Admin', 'Developer', 'Work-Bee', 'Standard'],
                datasets: [{
                    data: [
                        {{ $userStats['by_role']['admin'] ?? 0 }},
                        {{ $userStats['by_role']['developer'] ?? 0 }},
                        {{ $userStats['by_role']['work-bee'] ?? 0 }},
                        {{ $userStats['by_role']['standard'] ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: ['Pending', 'Approved', 'Implemented'],
                datasets: [{
                    data: [
                        {{ $ideaStats['pending'] }},
                        {{ $ideaStats['approved'] }},
                        {{ $ideaStats['implemented'] }}
                    ],
                    backgroundColor: [
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(168, 85, 247, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Activity Trends Chart
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($recentActivity['ideas_trend'] as $data)
                        '{{ \Carbon\Carbon::parse($data->date)->format('M d') }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'New Ideas',
                    data: [
                        @foreach($recentActivity['ideas_trend'] as $data)
                            {{ $data->count }},
                        @endforeach
                    ],
                    borderColor: 'rgba(251, 191, 36, 1)',
                    backgroundColor: 'rgba(251, 191, 36, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'New Users',
                    data: [
                        @foreach($recentActivity['users_trend'] as $data)
                            {{ $data->count }},
                        @endforeach
                    ],
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>
