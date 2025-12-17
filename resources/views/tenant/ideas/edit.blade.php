<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Idea - {{ $idea->title }} - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50" x-data="{ mobileMenuOpen: false }">

    @include('tenant.partials.nav')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <a href="{{ route('tenant.ideas.show', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" 
                   class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-edit text-indigo-600 mr-3"></i>
                        Edit Idea
                    </h1>
                    <p class="text-gray-600 mt-2">{{ $idea->problem_short }}</p>
                </div>
            </div>
        </div>

        <!-- Role Info Banner -->
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
                <div class="text-sm text-blue-800">
                    <p class="font-semibold mb-2">Your Edit Permissions ({{ ucfirst($user->role) }})</p>
                    <ul class="space-y-1">
                        @if($idea->canEditBasic($user))
                            <li><i class="fas fa-check text-green-600 mr-2"></i>Basic Info (Title, Problem, Goal, Description)</li>
                        @endif
                        @if($idea->canEditWorkBee($user))
                            <li><i class="fas fa-check text-green-600 mr-2"></i>Work-Bee Fields (Schmerz, Umsetzung)</li>
                        @endif
                        @if($idea->canEditDeveloper($user))
                            <li><i class="fas fa-check text-green-600 mr-2"></i>Developer Fields (Lösung, Dauer, Kosten)</li>
                        @endif
                        @if($user->isAdmin())
                            <li><i class="fas fa-check text-green-600 mr-2"></i>Admin (All Fields + Status)</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('tenant.ideas.update', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}">
            @csrf
            @method('PUT')

            <!-- Basic Info (Creator + Admin) -->
            @if($idea->canEditBasic($user))
                <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">
                        <i class="fas fa-file-alt text-indigo-600 mr-2"></i>
                        Basic Information
                    </h2>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Title *</label>
                            <input type="text" name="title" value="{{ old('title', $idea->title) }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Problem (Short) *</label>
                            <input type="text" name="problem_short" value="{{ old('problem_short', $idea->problem_short) }}" required maxlength="100"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Goal *</label>
                            <textarea name="goal" rows="3" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('goal', $idea->goal) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Description *</label>
                            <textarea name="description" rows="5" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('description', $idea->description) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Priority Level *</label>
                            <div class="grid grid-cols-4 gap-3">
                                @foreach(['low', 'medium', 'high', 'urgent'] as $priority)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="priority" value="{{ $priority }}" {{ $idea->priority === $priority ? 'checked' : '' }} class="peer sr-only">
                                        <div class="p-3 border-2 rounded-lg peer-checked:border-indigo-500 peer-checked:bg-indigo-50 text-center">
                                            <p class="font-semibold text-sm">{{ ucfirst($priority) }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                            <input type="email" name="submitter_email" value="{{ old('submitter_email', $idea->submitter_email) }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Developer Fields (Developer + Admin) -->
            @if($idea->canEditDeveloper($user))
                <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">
                        <i class="fas fa-code text-purple-600 mr-2"></i>
                        Developer Fields (Lösung, Dauer, Kosten)
                    </h2>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Lösung (Solution)</label>
                            <textarea name="solution" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                      placeholder="Describe the technical solution...">{{ old('solution', $idea->solution) }}</textarea>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Kosten (Cost)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-gray-500">$</span>
                                    <input type="number" name="cost_estimate" value="{{ old('cost_estimate', $idea->cost_estimate) }}" step="0.01" min="0"
                                           class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Used in calculation: (Cost / 100) + Duration</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Dauer (Duration)</label>
                                <input type="text" name="duration_estimate" value="{{ old('duration_estimate', $idea->duration_estimate) }}"
                                       placeholder="e.g., 3 days, 2 weeks"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                <p class="text-xs text-gray-500 mt-1">Used in calculation: (Cost / 100) + Duration</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Work-Bee Fields (Work-Bee + Admin) -->
            @if($idea->canEditWorkBee($user))
                <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">
                        <i class="fas fa-heartbeat text-green-600 mr-2"></i>
                        Work-Bee Fields (Schmerz, Umsetzung)
                    </h2>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Schmerz (Pain Score) *</label>
                            <div class="flex items-center space-x-4">
                                <input type="range" name="pain_score" value="{{ old('pain_score', $idea->pain_score) }}" 
                                       min="0" max="10" step="1" class="flex-1" 
                                       oninput="this.nextElementSibling.querySelector('span').textContent = this.value">
                                <div class="w-20 text-center">
                                    <span class="text-3xl font-bold text-orange-600">{{ old('pain_score', $idea->pain_score) }}</span>
                                    <p class="text-xs text-gray-600">/10</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Used in Prio 2: Prio 1 / Schmerz</p>
                        </div>

                        <div>
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="in_implementation" value="1" {{ $idea->in_implementation ? 'checked' : '' }}
                                       class="w-5 h-5 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                                <span class="text-sm font-semibold text-gray-700">Umsetzung (In Implementation)</span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Implementation Date</label>
                            <input type="date" name="implementation_date" value="{{ old('implementation_date', $idea->implementation_date?->format('Y-m-d')) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Admin Only - Status -->
            @if($user->isAdmin())
                <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">
                        <i class="fas fa-shield-alt text-red-600 mr-2"></i>
                        Admin Controls
                    </h2>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                            @foreach(['pending', 'in-review', 'approved', 'rejected', 'implemented'] as $status)
                                <option value="{{ $status }}" {{ $idea->status === $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('-', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <!-- Calculated Priorities (Read-Only) -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl shadow-lg p-8 mb-6 border border-indigo-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-calculator text-indigo-600 mr-2"></i>
                    Auto-Calculated Priorities
                </h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2">Prio 1 = (Kosten / 100) + Dauer</p>
                        <p class="text-3xl font-bold text-indigo-600">{{ number_format($idea->priority_1, 2) }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2">Prio 2 = Prio 1 / Schmerz</p>
                        <p class="text-3xl font-bold text-purple-600">{{ number_format($idea->priority_2, 2) }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-4">
                    <i class="fas fa-info-circle mr-1"></i>
                    These values are automatically calculated when you save
                </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between">
                <a href="{{ route('tenant.ideas.show', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" 
                   class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>

        </form>

    </div>

</body>
</html>
