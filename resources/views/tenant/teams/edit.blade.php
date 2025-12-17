<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Team: {{ $team->name }} - {{ $tenant->subdomain }}</title>
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
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .toggle-slider {
            background-color: #10B981;
        }
        input:checked + .toggle-slider:before {
            transform: translateX(26px);
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
                    <a href="{{ route('tenant.teams.show', ['tenantId' => $tenant->id, 'team' => $team->id]) }}" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Team
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
                <i class="fas fa-edit text-indigo-600 mr-3"></i>
                Edit Team
            </h1>
            <p class="text-gray-600 mt-2">Update team information and settings</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <form method="POST" action="{{ route('tenant.teams.update', ['tenantId' => $tenant->id, 'team' => $team->id]) }}" id="editTeamForm">
                @csrf
                @method('PUT')

                <!-- Form Content -->
                <div class="p-8 space-y-6">

                    <!-- Current Team Preview -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6 border border-gray-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white font-bold text-2xl shadow-lg" 
                                 style="background: {{ $team->color }}" id="teamPreviewIcon">
                                {{ strtoupper(substr($team->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Current Team</p>
                                <h3 class="text-xl font-bold text-gray-900" id="teamPreviewName">{{ $team->name }}</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    <i class="fas fa-users mr-1"></i>{{ $team->members->count() }} members
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Team Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tag text-gray-400 mr-2"></i>Team Name *
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name"
                            value="{{ old('name', $team->name) }}"
                            required
                            maxlength="255"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('name') border-red-500 @enderror"
                            placeholder="e.g., Development Team, Marketing Team"
                            oninput="updatePreview()"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
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
                        >{{ old('description', $team->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <span id="charCount">{{ strlen($team->description) }}</span> / 1000 characters
                        </p>
                    </div>

                    <!-- Team Color -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-palette text-gray-400 mr-2"></i>Team Color *
                        </label>
                        <input type="hidden" name="color" id="colorInput" value="{{ old('color', $team->color) }}">
                        
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
                                    class="color-option {{ old('color', $team->color) === $colorCode ? 'selected' : '' }}" 
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
                                value="{{ old('color', $team->color) }}"
                                class="w-16 h-10 border-2 border-gray-300 rounded cursor-pointer"
                                onchange="selectColor(this.value)"
                            >
                            <span class="text-sm text-gray-600 font-mono" id="colorCode">{{ old('color', $team->color) }}</span>
                        </div>

                        @error('color')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Team Status -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-semibold text-gray-700 flex items-center">
                                    <i class="fas fa-toggle-on text-gray-400 mr-2"></i>
                                    Team Status
                                </label>
                                <p class="text-sm text-gray-500 mt-1">
                                    Active teams are visible and accessible to all members
                                </p>
                            </div>
                            
                            <label class="toggle-switch">
                                <input 
                                    type="checkbox" 
                                    name="is_active" 
                                    value="1"
                                    {{ old('is_active', $team->is_active) ? 'checked' : '' }}
                                    onchange="updateStatusPreview(this)"
                                >
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="mt-4 p-4 rounded-lg" id="statusPreview">
                            @if(old('is_active', $team->is_active))
                                <div class="flex items-center text-green-700 bg-green-50">
                                    <i class="fas fa-check-circle text-xl mr-3"></i>
                                    <div>
                                        <p class="font-semibold">Team is Active</p>
                                        <p class="text-sm">Members can access and collaborate</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center text-gray-700 bg-gray-50">
                                    <i class="fas fa-pause-circle text-xl mr-3"></i>
                                    <div>
                                        <p class="font-semibold">Team is Inactive</p>
                                        <p class="text-sm">Members cannot access this team</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Team Metadata -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-info-circle text-gray-400 mr-2"></i>Team Information
                        </h4>
                        <div class="grid md:grid-cols-2 gap-4 text-sm">
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                <span class="text-gray-600">Created:</span>
                                <span class="ml-2 font-medium">{{ $team->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-user text-gray-400 mr-2"></i>
                                <span class="text-gray-600">Created by:</span>
                                <span class="ml-2 font-medium">{{ $team->creator->name }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-users text-gray-400 mr-2"></i>
                                <span class="text-gray-600">Total Members:</span>
                                <span class="ml-2 font-medium">{{ $team->members->count() }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock text-gray-400 mr-2"></i>
                                <span class="text-gray-600">Last Updated:</span>
                                <span class="ml-2 font-medium">{{ $team->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Form Actions -->
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('tenant.teams.show', ['tenantId' => $tenant->id, 'team' => $team->id]) }}" 
                           class="px-6 py-2 text-gray-700 hover:bg-gray-200 rounded-lg transition">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        
                        <button 
                            type="button"
                            onclick="resetForm()"
                            class="px-6 py-2 text-gray-700 hover:bg-gray-200 rounded-lg transition">
                            <i class="fas fa-undo mr-2"></i>Reset
                        </button>
                    </div>
                    
                    <button 
                        type="submit"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>

            </form>
        </div>

        <!-- Warning Card -->
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3 mt-1"></i>
                <div>
                    <h4 class="font-semibold text-yellow-900 mb-2">Important Notes:</h4>
                    <ul class="text-sm text-yellow-800 space-y-1">
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>Changes will affect all team members immediately</li>
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>Deactivating a team will restrict member access</li>
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>Team color helps with visual identification</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Delete Team Section -->
        <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h4 class="font-semibold text-red-900 mb-2 flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        Danger Zone
                    </h4>
                    <p class="text-sm text-red-800">
                        Deleting this team will remove all member associations. This action cannot be undone.
                    </p>
                </div>
                <form method="POST" action="{{ route('tenant.teams.destroy', ['tenantId' => $tenant->id, 'team' => $team->id]) }}" 
                      onsubmit="return confirm('⚠️ Are you absolutely sure?\n\nThis will permanently delete the team:\n• Team: {{ $team->name }}\n• Members: {{ $team->members->count() }}\n\nThis action CANNOT be undone!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition whitespace-nowrap">
                        <i class="fas fa-trash-alt mr-2"></i>Delete Team
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- JavaScript -->
    <script>
        // Color selection
        function selectColor(color) {
            document.getElementById('colorInput').value = color;
            document.getElementById('colorCode').textContent = color;
            document.getElementById('customColor').value = color;
            
            // Update selected state
            document.querySelectorAll('.color-option').forEach(el => {
                el.classList.remove('selected');
            });
            
            const selectedOption = document.querySelector(`.color-option[style*="${color}"]`);
            if (selectedOption) {
                selectedOption.classList.add('selected');
            }
            
            updatePreview();
        }

        // Update preview
        function updatePreview() {
            const name = document.getElementById('name').value;
            const color = document.getElementById('colorInput').value;
            
            // Update preview name
            document.getElementById('teamPreviewName').textContent = name || 'Team Name';
            
            // Update preview icon
            const icon = document.getElementById('teamPreviewIcon');
            icon.style.backgroundColor = color;
            icon.textContent = name ? name.charAt(0).toUpperCase() : 'T';
        }

        // Update status preview
        function updateStatusPreview(checkbox) {
            const preview = document.getElementById('statusPreview');
            
            if (checkbox.checked) {
                preview.innerHTML = `
                    <div class="flex items-center text-green-700 bg-green-50 p-4 rounded-lg">
                        <i class="fas fa-check-circle text-xl mr-3"></i>
                        <div>
                            <p class="font-semibold">Team is Active</p>
                            <p class="text-sm">Members can access and collaborate</p>
                        </div>
                    </div>
                `;
            } else {
                preview.innerHTML = `
                    <div class="flex items-center text-gray-700 bg-gray-50 p-4 rounded-lg">
                        <i class="fas fa-pause-circle text-xl mr-3"></i>
                        <div>
                            <p class="font-semibold">Team is Inactive</p>
                            <p class="text-sm">Members cannot access this team</p>
                        </div>
                    </div>
                `;
            }
        }

        // Character counter
        const descriptionInput = document.getElementById('description');
        const charCount = document.getElementById('charCount');
        
        descriptionInput.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });

        // Reset form
        function resetForm() {
            if (confirm('Reset all changes to original values?')) {
                document.getElementById('editTeamForm').reset();
                selectColor('{{ $team->color }}');
                charCount.textContent = '{{ strlen($team->description) }}';
                updatePreview();
            }
        }

        // Form validation
        document.getElementById('editTeamForm').addEventListener('submit', function(e) {
            const teamName = document.getElementById('name').value.trim();
            
            if (teamName.length < 3) {
                e.preventDefault();
                alert('Team name must be at least 3 characters long');
                document.getElementById('name').focus();
                return false;
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updatePreview();
        });
    </script>

</body>
</html>
