<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit New Idea - {{ $currentTeam->name }} - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .tag-input {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            min-height: 2.5rem;
        }
        .tag-item {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            background: #EEF2FF;
            color: #4F46E5;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .tag-item button {
            margin-left: 0.5rem;
            color: #6366F1;
            cursor: pointer;
        }
        .char-counter {
            font-size: 0.75rem;
            color: #6B7280;
        }
        .char-counter.warning {
            color: #F59E0B;
        }
        .char-counter.danger {
            color: #EF4444;
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ mobileMenuOpen: false }">

    @include('tenant.partials.nav')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <a href="{{ route('tenant.ideas.index', ['tenantId' => $tenant->id]) }}" 
                   class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-lightbulb text-yellow-500 mr-3"></i>
                        Submit New Idea
                    </h1>
                    <p class="text-gray-600 mt-2">Share your innovative idea with your team</p>
                </div>
            </div>
        </div>

        <!-- Current Team Banner -->
        <div class="mb-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg" 
                         style="background: {{ $currentTeam->color }}">
                        {{ strtoupper(substr($currentTeam->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm text-indigo-100 mb-1">
                            <i class="fas fa-users mr-1"></i>Submitting to Team
                        </p>
                        <h3 class="text-xl font-bold">{{ $currentTeam->name }}</h3>
                        <p class="text-sm text-indigo-100 mt-1">{{ $currentTeam->member_count }} members will see this</p>
                    </div>
                </div>
                <a href="{{ route('tenant.my-teams', ['tenantId' => $tenant->id]) }}" 
                   class="px-4 py-2 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-indigo-50 transition">
                    <i class="fas fa-exchange-alt mr-2"></i>Switch Team
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <form method="POST" action="{{ route('tenant.ideas.store', ['tenantId' => $tenant->id]) }}" id="ideaForm">
                @csrf

                <!-- Form Content -->
                <div class="p-8 space-y-6">

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-heading text-gray-400 mr-2"></i>Idea Title *
                        </label>
                        <input 
                            type="text" 
                            name="title" 
                            id="title"
                            value="{{ old('title') }}"
                            required
                            maxlength="255"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('title') border-red-500 @enderror"
                            placeholder="Give your idea a catchy title..."
                            oninput="updateCharCount('title', 255)"
                        >
                        <div class="flex justify-between mt-1">
                            @error('title')
                                <p class="text-sm text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @else
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>Clear and concise title that captures the essence
                                </p>
                            @enderror
                            <span class="char-counter" id="title-count">0 / 255</span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-align-left text-gray-400 mr-2"></i>Detailed Description *
                        </label>
                        <textarea 
                            name="description" 
                            id="description"
                            rows="8"
                            required
                            maxlength="5000"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('description') border-red-500 @enderror"
                            placeholder="Describe your idea in detail. Include:&#10;• What problem does it solve?&#10;• How would it work?&#10;• What benefits would it bring?&#10;• Any implementation ideas?"
                            oninput="updateCharCount('description', 5000)"
                        >{{ old('description') }}</textarea>
                        <div class="flex justify-between mt-1">
                            @error('description')
                                <p class="text-sm text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @else
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>Be as detailed as possible to help others understand your vision
                                </p>
                            @enderror
                            <span class="char-counter" id="description-count">0 / 5000</span>
                        </div>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-flag text-gray-400 mr-2"></i>Priority Level *
                        </label>
                        <div class="grid md:grid-cols-4 gap-4">
                            <label class="priority-option cursor-pointer">
                                <input type="radio" name="priority" value="low" class="peer sr-only" {{ old('priority') === 'low' ? 'checked' : '' }}>
                                <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-gray-500 peer-checked:bg-gray-50 hover:border-gray-400 transition">
                                    <div class="flex items-center justify-between mb-2">
                                        <i class="fas fa-flag text-gray-500 text-2xl"></i>
                                        <span class="hidden peer-checked:inline text-gray-600">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    </div>
                                    <p class="font-semibold text-gray-900">Low</p>
                                    <p class="text-xs text-gray-600 mt-1">Nice to have</p>
                                </div>
                            </label>

                            <label class="priority-option cursor-pointer">
                                <input type="radio" name="priority" value="medium" class="peer sr-only" {{ old('priority', 'medium') === 'medium' ? 'checked' : '' }}>
                                <div class="p-4 border-2 border-blue-200 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-400 transition">
                                    <div class="flex items-center justify-between mb-2">
                                        <i class="fas fa-flag text-blue-500 text-2xl"></i>
                                        <span class="hidden peer-checked:inline text-blue-600">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    </div>
                                    <p class="font-semibold text-gray-900">Medium</p>
                                    <p class="text-xs text-gray-600 mt-1">Should implement</p>
                                </div>
                            </label>

                            <label class="priority-option cursor-pointer">
                                <input type="radio" name="priority" value="high" class="peer sr-only" {{ old('priority') === 'high' ? 'checked' : '' }}>
                                <div class="p-4 border-2 border-orange-200 rounded-lg peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:border-orange-400 transition">
                                    <div class="flex items-center justify-between mb-2">
                                        <i class="fas fa-flag text-orange-500 text-2xl"></i>
                                        <span class="hidden peer-checked:inline text-orange-600">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    </div>
                                    <p class="font-semibold text-gray-900">High</p>
                                    <p class="text-xs text-gray-600 mt-1">Important change</p>
                                </div>
                            </label>

                            <label class="priority-option cursor-pointer">
                                <input type="radio" name="priority" value="urgent" class="peer sr-only" {{ old('priority') === 'urgent' ? 'checked' : '' }}>
                                <div class="p-4 border-2 border-red-200 rounded-lg peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-400 transition">
                                    <div class="flex items-center justify-between mb-2">
                                        <i class="fas fa-flag text-red-500 text-2xl"></i>
                                        <span class="hidden peer-checked:inline text-red-600">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    </div>
                                    <p class="font-semibold text-gray-900">Urgent</p>
                                    <p class="text-xs text-gray-600 mt-1">Critical issue</p>
                                </div>
                            </label>
                        </div>
                        @error('priority')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Tags -->
                    <div>
                        <label for="tags-input" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tags text-gray-400 mr-2"></i>Tags (Optional)
                        </label>
                        <div id="tag-container" class="tag-input" onclick="document.getElementById('tags-input').focus()">
                            <input 
                                type="text" 
                                id="tags-input"
                                class="flex-1 border-0 outline-none min-w-[120px]"
                                placeholder="Type and press Enter..."
                                onkeydown="handleTagInput(event)"
                            >
                        </div>
                        <input type="hidden" name="tags" id="tags-hidden">
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Add relevant tags to help categorize your idea (e.g., UI, Backend, Feature, Improvement)
                        </p>
                        @error('tags')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Preview Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-eye text-indigo-600 mr-2"></i>Preview
                        </h3>
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <h4 class="text-xl font-bold text-gray-900 mb-3" id="preview-title">
                                Your Idea Title
                            </h4>
                            <p class="text-gray-700 whitespace-pre-line mb-4" id="preview-description">
                                Your detailed description will appear here...
                            </p>
                            <div class="flex items-center space-x-2" id="preview-tags"></div>
                        </div>
                    </div>

                </div>

                <!-- Form Actions -->
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <a href="{{ route('tenant.ideas.index', ['tenantId' => $tenant->id]) }}" 
                       class="px-6 py-2 text-gray-700 hover:bg-gray-200 rounded-lg transition">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button 
                        type="submit"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Idea
                    </button>
                </div>

            </form>
        </div>

        <!-- Guidelines Card -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-500 text-2xl mr-4"></i>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-2">💡 Tips for Great Ideas</h4>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li><i class="fas fa-check text-blue-500 mr-2"></i><strong>Be Specific:</strong> Clearly explain what problem your idea solves</li>
                        <li><i class="fas fa-check text-blue-500 mr-2"></i><strong>Think Big:</strong> Don't hold back - even wild ideas can spark innovation</li>
                        <li><i class="fas fa-check text-blue-500 mr-2"></i><strong>Add Context:</strong> Explain why this idea matters and who it would help</li>
                        <li><i class="fas fa-check text-blue-500 mr-2"></i><strong>Consider Feasibility:</strong> Think about how it could realistically be implemented</li>
                        <li><i class="fas fa-check text-blue-500 mr-2"></i><strong>Use Tags:</strong> Proper tags help team members find related ideas</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <!-- JavaScript -->
    <script>
        // Tags management
        let tags = [];

        function handleTagInput(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const input = event.target;
                const value = input.value.trim();
                
                if (value && !tags.includes(value)) {
                    tags.push(value);
                    addTagToUI(value);
                    input.value = '';
                    updateTagsHidden();
                    updatePreview();
                }
            }
        }

        function addTagToUI(tag) {
            const container = document.getElementById('tag-container');
            const input = document.getElementById('tags-input');
            
            const tagElement = document.createElement('span');
            tagElement.className = 'tag-item';
            tagElement.innerHTML = `
                ${tag}
                <button type="button" onclick="removeTag('${tag}')">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            container.insertBefore(tagElement, input);
        }

        function removeTag(tag) {
            tags = tags.filter(t => t !== tag);
            updateTagsUI();
            updateTagsHidden();
            updatePreview();
        }

        function updateTagsUI() {
            const container = document.getElementById('tag-container');
            const input = document.getElementById('tags-input');
            
            // Remove all tag elements
            container.querySelectorAll('.tag-item').forEach(el => el.remove());
            
            // Re-add all tags
            tags.forEach(tag => addTagToUI(tag));
        }

        function updateTagsHidden() {
            document.getElementById('tags-hidden').value = tags.join(',');
        }

        // Character counters
        function updateCharCount(field, max) {
            const input = document.getElementById(field);
            const counter = document.getElementById(`${field}-count`);
            const length = input.value.length;
            
            counter.textContent = `${length} / ${max}`;
            
            // Update color based on usage
            counter.classList.remove('warning', 'danger');
            if (length > max * 0.9) {
                counter.classList.add('danger');
            } else if (length > max * 0.75) {
                counter.classList.add('warning');
            }
            
            updatePreview();
        }

        // Live preview
        function updatePreview() {
            const title = document.getElementById('title').value || 'Your Idea Title';
            const description = document.getElementById('description').value || 'Your detailed description will appear here...';
            
            document.getElementById('preview-title').textContent = title;
            document.getElementById('preview-description').textContent = description;
            
            // Update tags preview
            const tagsPreview = document.getElementById('preview-tags');
            tagsPreview.innerHTML = '';
            
            tags.forEach(tag => {
                const span = document.createElement('span');
                span.className = 'px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded';
                span.textContent = `#${tag}`;
                tagsPreview.appendChild(span);
            });
        }

        // Initialize character counters
        document.addEventListener('DOMContentLoaded', function() {
            updateCharCount('title', 255);
            updateCharCount('description', 5000);
            
            // Add input listeners for live preview
            document.getElementById('title').addEventListener('input', updatePreview);
            document.getElementById('description').addEventListener('input', updatePreview);
        });

        // Form validation
        document.getElementById('ideaForm').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            
            if (title.length < 10) {
                e.preventDefault();
                alert('Title must be at least 10 characters long');
                document.getElementById('title').focus();
                return false;
            }
            
            if (description.length < 50) {
                e.preventDefault();
                alert('Description must be at least 50 characters long to provide enough detail');
                document.getElementById('description').focus();
                return false;
            }
        });
    </script>

</body>
</html>
