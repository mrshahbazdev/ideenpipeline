<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Team - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .color-option {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s;
        }
        .color-option:hover {
            transform: scale(1.1);
        }
        .color-option.selected {
            border-color: #1F2937;
            transform: scale(1.15);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .member-checkbox:checked + label {
            background: #EEF2FF;
            border-color: #6366F1;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" class="text-xl font-bold text-indigo-600">
                        <i class="fas fa-building mr-2"></i>{{ $tenant->subdomain }}
                    </a>
                    <span class="ml-3 px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                        <i class="fas fa-crown mr-1"></i>ADMIN
                    </span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('tenant.teams.index', ['tenantId' => $tenant->id]) }}" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Teams
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-users-cog text-indigo-600 mr-3"></i>
                Create New Team
            </h1>
            <p class="text-gray-600 mt-2">Organize your team members into groups for better collaboration</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <form method="POST" action="{{ route('tenant.teams.store', ['tenantId' => $tenant->id]) }}" id="createTeamForm">
                @csrf

                <!-- Form Content -->
                <div class="p-8 space-y-6">

                    <!-- Team Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tag text-gray-400 mr-2"></i>Team Name *
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name"
                            value="{{ old('name') }}"
                            required
                            maxlength="255"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('name') border-red-500 @enderror"
                            placeholder="e.g., Development Team, Marketing Team"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>Choose a descriptive name for your team
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-align-left text-gray-400 mr-2"></i>Description
                        </label>
                        <textarea 
                            name="description" 
                            id="description"
                            rows="4"
                            maxlength="1000"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('description') border-red-500 @enderror"
                            placeholder="Describe the team's purpose and responsibilities..."
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <span id="charCount">0</span> / 1000 characters
                        </p>
                    </div>

                    <!-- Team Color -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-palette text-gray-400 mr-2"></i>Team Color *
                        </label>
                        <input type="hidden" name="color" id="colorInput" value="{{ old('color', '#3B82F6') }}">
                        
                        <div class="grid grid-cols-8 md:grid-cols-12 gap-3 mb-4">
                            @php
                                $colors = [
                                    '#3B82F6' => 'Blue',
                                    '#10B981' => 'Green',
                                    '#EF4444' => 'Red',
                                    '#F59E0B' => 'Orange',
                                    '#8B5CF6' => 'Purple',
                                    '#EC4899' => 'Pink',
                                    '#14B8A6' => 'Teal',
                                    '#F97316' => 'Orange',
                                    '#6366F1' => 'Indigo',
                                    '#06B6D4' => 'Cyan',
                                    '#84CC16' => 'Lime',
                                    '#A855F7' => 'Violet',
                                ];
                            @endphp
                            
                            @foreach($colors as $colorCode => $colorName)
                                <div 
                                    class="color-option {{ old('color', '#3B82F6') === $colorCode ? 'selected' : '' }}" 
                                    style="background-color: {{ $colorCode }}"
                                    onclick="selectColor('{{ $colorCode }}')"
                                    title="{{ $colorName }}"
                                ></div>
                            @endforeach
                        </div>

                        <!-- Custom Color Picker -->
                        <div class="flex items-center space-x-3">
                            <label class="text-sm text-gray-600">Or choose custom color:</label>
                            <input 
                                type="color" 
                                id="customColor"
                                class="w-16 h-10 border-2 border-gray-300 rounded cursor-pointer"
                                onchange="selectColor(this.value)"
                            >
                        </div>

                        @error('color')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Team Members -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-users text-gray-400 mr-2"></i>Team Members
                        </label>
                        
                        @if($availableUsers->count() > 0)
                            <!-- Search Box -->
                            <div class="mb-4">
                                <input 
                                    type="text" 
                                    id="memberSearch"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Search members..."
                                    onkeyup="filterMembers()"
                                >
                            </div>

                            <!-- Members List -->
                            <div class="border border-gray-200 rounded-lg max-h-96 overflow-y-auto">
                                <div id="membersList">
                                    @foreach($availableUsers as $user)
                                        <div class="member-item p-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition">
                                            <div class="flex items-center">
                                                <input 
                                                    type="checkbox" 
                                                    name="members[]" 
                                                    value="{{ $user->id }}"
                                                    id="member_{{ $user->id }}"
                                                    class="member-checkbox w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                                    {{ in_array($user->id, old('members', [])) ? 'checked' : '' }}
                                                >
                                                <label for="member_{{ $user->id }}" class="ml-3 flex-1 flex items-center cursor-pointer">
                                                    <!-- Avatar -->
                                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold
                                                        {{ $user->role === 'developer' ? 'bg-purple-500' : '' }}
                                                        {{ $user->role === 'work-bee' ? 'bg-green-500' : '' }}
                                                        {{ $user->role === 'standard' ? 'bg-blue-500' : '' }}
                                                    ">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                    
                                                    <!-- Info -->
                                                    <div class="ml-3 flex-1">
                                                        <p class="text-sm font-medium text-gray-900 member-name">{{ $user->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                                    </div>

                                                    <!-- Role Badge -->
                                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $user->getRoleBadgeClass() }}">
                                                        <i class="fas {{ $user->getRoleIcon() }} mr-1"></i>
                                                        {{ ucfirst(str_replace('-', ' ', $user->role)) }}
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Selection Counter -->
                            <p class="mt-3 text-sm text-gray-600">
                                <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                <span id="selectedCount">{{ count(old('members', [])) }}</span> member(s) selected
                            </p>

                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
                                <i class="fas fa-users text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-600">No users available to add</p>
                                <p class="text-sm text-gray-500 mt-1">Invite team members first</p>
                            </div>
                        @endif

                        @error('members')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                <!-- Form Actions -->
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <a href="{{ route('tenant.teams.index', ['tenantId' => $tenant->id]) }}" class="px-6 py-2 text-gray-700 hover:bg-gray-200 rounded-lg transition">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button 
                        type="submit"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition transform hover:scale-105"
                    >
                        <i class="fas fa-check mr-2"></i>Create Team
                    </button>
                </div>

            </form>
        </div>

        <!-- Help Card -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-2">Tips for creating teams:</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li><i class="fas fa-check text-blue-500 mr-2"></i>Choose a clear and descriptive team name</li>
                        <li><i class="fas fa-check text-blue-500 mr-2"></i>Add a description to clarify the team's purpose</li>
                        <li><i class="fas fa-check text-blue-500 mr-2"></i>Select a distinctive color for easy identification</li>
                        <li><i class="fas fa-check text-blue-500 mr-2"></i>Add members now or later from team details page</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <!-- JavaScript -->
    <script>
        // Color selection
        function selectColor(color) {
            document.getElementById('colorInput').value = color;
            
            // Update selected state
            document.querySelectorAll('.color-option').forEach(el => {
                el.classList.remove('selected');
            });
            
            const selectedOption = document.querySelector(`.color-option[style*="${color}"]`);
            if (selectedOption) {
                selectedOption.classList.add('selected');
            }
        }

        // Character counter for description
        const descriptionInput = document.getElementById('description');
        const charCount = document.getElementById('charCount');
        
        if (descriptionInput) {
            descriptionInput.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
            
            // Initialize count
            charCount.textContent = descriptionInput.value.length;
        }

        // Member selection counter
        const checkboxes = document.querySelectorAll('.member-checkbox');
        const selectedCount = document.getElementById('selectedCount');
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });
        
        function updateSelectedCount() {
            const checked = document.querySelectorAll('.member-checkbox:checked').length;
            selectedCount.textContent = checked;
        }

        // Member search filter
        function filterMembers() {
            const searchTerm = document.getElementById('memberSearch').value.toLowerCase();
            const members = document.querySelectorAll('.member-item');
            
            members.forEach(member => {
                const name = member.querySelector('.member-name').textContent.toLowerCase();
                const email = member.querySelector('.text-xs').textContent.toLowerCase();
                
                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    member.style.display = '';
                } else {
                    member.style.display = 'none';
                }
            });
        }

        // Form validation
        document.getElementById('createTeamForm').addEventListener('submit', function(e) {
            const teamName = document.getElementById('name').value.trim();
            
            if (teamName.length < 3) {
                e.preventDefault();
                alert('Team name must be at least 3 characters long');
                document.getElementById('name').focus();
                return false;
            }
        });
    </script>

</body>
</html>
