<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .status-active { background-color: #d1fae5; border-color: #34d399; }
        .status-suspended { background-color: #fef3c7; border-color: #f59e0b; }
        .status-expired { background-color: #fee2e2; border-color: #ef4444; }
        
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ mobileMenuOpen: false }">

    @include('tenant.partials.nav')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 fade-in">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-cog text-indigo-600 mr-3"></i>
                Tenant Settings
            </h1>
            <p class="text-gray-600 mt-2">Configure your organization settings</p>

            <!-- Breadcrumb -->
            <div class="flex items-center space-x-2 text-sm text-gray-600 mt-4">
                <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" 
                   class="hover:text-indigo-600 transition-colors">
                    Dashboard
                </a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900 font-medium">Settings</span>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm animate-pulse" 
                 x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-green-600 hover:text-green-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm" 
                 x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <p class="text-red-800 font-medium">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <form method="POST" 
              action="{{ route('tenant.admin.settings.update', ['tenantId' => $tenant->id]) }}" 
              id="settingsForm"
              x-data="{
                  originalSubdomain: '{{ $tenant->subdomain }}',
                  originalStatus: '{{ $tenant->status }}',
                  isSubmitting: false
              }"
              @submit.prevent="
                  if(isSubmitting) return false;
                  
                  // Confirm subdomain change
                  if(document.getElementById('subdomain').value !== originalSubdomain) {
                      if(!confirm('⚠️ Changing the subdomain will change your organization\\'s URL. Are you sure you want to continue?')) {
                          return false;
                      }
                  }
                  
                  // Confirm status change to suspended/expired
                  const statusSelect = document.getElementById('status');
                  if(statusSelect.value !== originalStatus && 
                     (statusSelect.value === 'suspended' || statusSelect.value === 'expired')) {
                      const statusText = statusSelect.options[statusSelect.selectedIndex].text;
                      if(!confirm(`⚠️ Changing status to \"${statusText}\" will affect all users. Continue?`)) {
                          return false;
                      }
                  }
                  
                  // Show loading state
                  isSubmitting = true;
                  const submitBtn = document.querySelector('[type=\"submit\"]');
                  const originalText = submitBtn.innerHTML;
                  submitBtn.innerHTML = '<i class=\"fas fa-spinner fa-spin mr-2\"></i>Saving...';
                  submitBtn.disabled = true;
                  
                  // Submit form
                  $el.submit();
              ">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-6 border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-building text-indigo-600 mr-2"></i>
                    Organization Information
                </h2>

                <div class="space-y-6">
                    <!-- Subdomain -->
                    <div>
                        <label for="subdomain" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-link text-gray-400 mr-2"></i>Subdomain *
                        </label>
                        <div class="flex items-center">
                            <input 
                                type="text" 
                                name="subdomain" 
                                id="subdomain"
                                value="{{ old('subdomain', $tenant->subdomain) }}"
                                required
                                pattern="[a-zA-Z0-9-]+"
                                title="Only letters, numbers, and hyphens are allowed"
                                maxlength="63"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('subdomain') border-red-500 @enderror"
                                placeholder="your-company"
                                x-model="subdomain"
                                @input="document.getElementById('subdomain-display').textContent = $event.target.value + '.ideenpipeline.de'"
                            >
                            <span class="px-4 py-3 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-gray-600 font-mono">
                                .ideenpipeline.de
                            </span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Your organization's unique identifier. Changing this will affect your URL.
                            </p>
                            <span class="text-xs text-indigo-600 font-medium" id="subdomain-display">
                                {{ $tenant->subdomain }}.ideenpipeline.de
                            </span>
                        </div>
                        @error('subdomain')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Tenant ID (Read-only) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-fingerprint text-gray-400 mr-2"></i>Tenant ID
                        </label>
                        <div class="flex items-center">
                            <input 
                                type="text" 
                                value="{{ $tenant->id }}"
                                disabled
                                class="flex-1 px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed font-mono text-sm"
                            >
                            <button type="button" 
                                    class="ml-2 px-3 py-2 text-xs text-gray-600 hover:text-indigo-600"
                                    @click="navigator.clipboard.writeText('{{ $tenant->id }}'); alert('Tenant ID copied to clipboard!')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-lock mr-1"></i>This is your unique tenant identifier and cannot be changed.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Status & Subscription -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-6 border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-toggle-on text-indigo-600 mr-2"></i>
                    Status & Subscription
                </h2>

                <div class="space-y-6">
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-circle text-gray-400 mr-2"></i>Account Status *
                        </label>
                        <select 
                            name="status" 
                            id="status"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('status') border-red-500 @enderror"
                            x-model="currentStatus"
                        >
                            <option value="active" {{ old('status', $tenant->status) === 'active' ? 'selected' : '' }}>
                                ✅ Active - Fully operational
                            </option>
                            <option value="suspended" {{ old('status', $tenant->status) === 'suspended' ? 'selected' : '' }}>
                                ⏸️ Suspended - Temporarily disabled
                            </option>
                            <option value="expired" {{ old('status', $tenant->status) === 'expired' ? 'selected' : '' }}>
                                ⏰ Expired - Subscription ended
                            </option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Current Status Display -->
                    <div class="p-4 rounded-lg transition-colors" 
                         :class="{
                             'status-active': currentStatus === 'active',
                             'status-suspended': currentStatus === 'suspended',
                             'status-expired': currentStatus === 'expired'
                         }"
                         x-init="currentStatus = '{{ $tenant->status }}'">
                        <div class="flex items-center">
                            <i class="fas fa-circle text-sm mr-2" 
                               :class="{
                                   'text-green-500': currentStatus === 'active',
                                   'text-yellow-500': currentStatus === 'suspended',
                                   'text-red-500': currentStatus === 'expired'
                               }"></i>
                            <p class="text-sm font-semibold" 
                               :class="{
                                   'text-green-800': currentStatus === 'active',
                                   'text-yellow-800': currentStatus === 'suspended',
                                   'text-red-800': currentStatus === 'expired'
                               }">
                                Current Status: <span x-text="currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1)"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Expiration Date -->
                    <div>
                        <label for="expires_at" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar text-gray-400 mr-2"></i>Subscription Expires
                        </label>
                        <input 
                            type="date" 
                            name="expires_at" 
                            id="expires_at"
                            value="{{ old('expires_at', $tenant->expires_at ? $tenant->expires_at->format('Y-m-d') : '') }}"
                            min="{{ now()->format('Y-m-d') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('expires_at') border-red-500 @enderror"
                        >
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Leave empty for no expiration date.
                            </p>
                            @if($tenant->expires_at)
                                <span class="text-xs {{ $tenant->expires_at->isPast() ? 'text-red-500' : 'text-green-500' }}">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $tenant->expires_at->isPast() ? 'Expired ' : '' }}{{ $tenant->expires_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                        @error('expires_at')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-lg p-8 mb-6 border border-blue-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Account Information
                </h2>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-white/80 p-4 rounded-lg backdrop-blur-sm">
                        <p class="text-xs text-gray-600 mb-1 flex items-center">
                            <i class="fas fa-calendar-plus mr-2"></i>Created
                        </p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $tenant->created_at->format('F d, Y') }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $tenant->created_at->diffForHumans() }}</p>
                    </div>

                    <div class="bg-white/80 p-4 rounded-lg backdrop-blur-sm">
                        <p class="text-xs text-gray-600 mb-1 flex items-center">
                            <i class="fas fa-history mr-2"></i>Last Updated
                        </p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $tenant->updated_at->format('F d, Y') }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $tenant->updated_at->diffForHumans() }}</p>
                    </div>

                    @if($tenant->expires_at)
                        <div class="bg-white/80 p-4 rounded-lg backdrop-blur-sm">
                            <p class="text-xs text-gray-600 mb-1 flex items-center">
                                <i class="fas fa-hourglass-end mr-2"></i>Expires
                            </p>
                            <p class="text-sm font-semibold {{ $tenant->expires_at->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $tenant->expires_at->format('F d, Y') }}
                            </p>
                            <p class="text-xs {{ $tenant->expires_at->isPast() ? 'text-red-500' : 'text-gray-500' }}">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $tenant->expires_at->isPast() ? 'Expired ' : '' }}{{ $tenant->expires_at->diffForHumans() }}
                            </p>
                        </div>
                    @endif

                    <div class="bg-white/80 p-4 rounded-lg backdrop-blur-sm">
                        <p class="text-xs text-gray-600 mb-1 flex items-center">
                            <i class="fas fa-external-link-alt mr-2"></i>Access URL
                        </p>
                        <a href="https://{{ $tenant->subdomain }}.ideenpipeline.de" 
                           target="_blank"
                           class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors break-all flex items-center">
                            <span id="current-url">{{ $tenant->subdomain }}.ideenpipeline.de</span>
                            <i class="fas fa-external-link-alt text-xs ml-2"></i>
                        </a>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-mouse-pointer mr-1"></i>Click to open in new tab
                        </p>
                    </div>
                </div>
            </div>

            <!-- Warning -->
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-lg mb-6"
                 x-data="{ showWarning: true }" 
                 x-show="showWarning"
                 x-transition>
                <div class="flex justify-between items-start">
                    <div class="flex items-start flex-1">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3 mt-1"></i>
                        <div>
                            <p class="text-sm font-semibold text-yellow-900 mb-1">Important Notice</p>
                            <p class="text-xs text-yellow-800">
                                Changing the subdomain or status will affect all users. Make sure you understand the implications before saving changes.
                            </p>
                        </div>
                    </div>
                    <button @click="showWarning = false" 
                            class="text-yellow-600 hover:text-yellow-800 ml-2">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                <div class="flex space-x-3">
                    <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" 
                       class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition font-semibold flex items-center"
                       formnovalidate>
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                    
                    <button type="reset" 
                            class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition font-semibold flex items-center">
                        <i class="fas fa-undo mr-2"></i>Reset Changes
                    </button>
                </div>
                
                <button 
                    type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-semibold hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center min-w-[140px]"
                    aria-label="Save Settings"
                    :disabled="isSubmitting">
                    <i class="fas fa-save mr-2"></i>
                    <span x-text="isSubmitting ? 'Saving...' : 'Save Settings'"></span>
                </button>
            </div>

        </form>

    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        // Update current URL display when subdomain changes
        document.addEventListener('DOMContentLoaded', function() {
            const subdomainInput = document.getElementById('subdomain');
            const currentUrlDisplay = document.getElementById('current-url');
            const subdomainDisplay = document.getElementById('subdomain-display');
            
            if (subdomainInput) {
                subdomainInput.addEventListener('input', function() {
                    const newSubdomain = this.value.trim();
                    const fullUrl = newSubdomain ? newSubdomain + '.ideenpipeline.de' : '';
                    currentUrlDisplay.textContent = fullUrl;
                    subdomainDisplay.textContent = fullUrl;
                });
            }
            
            // Show confirmation when leaving page with unsaved changes
            window.addEventListener('beforeunload', function (e) {
                const form = document.getElementById('settingsForm');
                if (form) {
                    const formData = new FormData(form);
                    let hasChanges = false;
                    
                    // Check if any field value differs from original
                    const originalSubdomain = '{{ $tenant->subdomain }}';
                    const originalStatus = '{{ $tenant->status }}';
                    const originalExpiresAt = '{{ $tenant->expires_at ? $tenant->expires_at->format("Y-m-d") : "" }}';
                    
                    const currentSubdomain = document.getElementById('subdomain')?.value;
                    const currentStatus = document.getElementById('status')?.value;
                    const currentExpiresAt = document.getElementById('expires_at')?.value;
                    
                    if (currentSubdomain !== originalSubdomain ||
                        currentStatus !== originalStatus ||
                        currentExpiresAt !== originalExpiresAt) {
                        hasChanges = true;
                    }
                    
                    if (hasChanges) {
                        e.preventDefault();
                        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                        return 'You have unsaved changes. Are you sure you want to leave?';
                    }
                }
            });
            
            // Reset form warning
            const resetButton = document.querySelector('button[type="reset"]');
            if (resetButton) {
                resetButton.addEventListener('click', function() {
                    if (confirm('Are you sure you want to reset all changes?')) {
                        // Reload page to get original values
                        window.location.reload();
                    }
                });
            }
        });
    </script>

</body>
</html>