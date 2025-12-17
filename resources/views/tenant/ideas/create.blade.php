<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit New Idea - {{ $currentTeam->name }} - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .step-item {
            position: relative;
        }
        .step-item::after {
            content: '';
            position: absolute;
            top: 2rem;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #E5E7EB;
            z-index: -1;
        }
        .step-item:last-child::after {
            display: none;
        }
        .step-active .step-circle {
            background: #4F46E5;
            color: white;
            border-color: #4F46E5;
        }
        .step-completed .step-circle {
            background: #10B981;
            color: white;
            border-color: #10B981;
        }
    </style>
</head>
<body class="bg-gray-50" x-data="ideaWizard()" x-init="init()">

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
                    <p class="text-gray-600 mt-2">Tell us about your problem and idea</p>
                </div>
            </div>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8 bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="step-item flex-1 text-center"
                         :class="{
                             'step-active': currentStep === index + 1,
                             'step-completed': currentStep > index + 1
                         }">
                        <div class="step-circle w-16 h-16 mx-auto rounded-full border-4 border-gray-300 flex items-center justify-center font-bold text-lg bg-white mb-2">
                            <span x-show="currentStep <= index + 1" x-text="index + 1"></span>
                            <i x-show="currentStep > index + 1" class="fas fa-check"></i>
                        </div>
                        <p class="text-sm font-medium" x-text="step.title"></p>
                    </div>
                </template>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('tenant.ideas.store', ['tenantId' => $tenant->id]) }}" @submit="handleSubmit">
            @csrf

            <input type="hidden" name="problem_short" x-model="formData.problem_short">
            <input type="hidden" name="goal" x-model="formData.goal">
            <input type="hidden" name="description" x-model="formData.description">
            <input type="hidden" name="solution" x-model="formData.solution">
            <input type="hidden" name="pain_score" x-model="formData.pain_score">
            <input type="hidden" name="cost_estimate" x-model="formData.cost_estimate">
            <input type="hidden" name="duration_estimate" x-model="formData.duration_estimate">
            <input type="hidden" name="priority" x-model="formData.priority">
            <input type="hidden" name="submitter_email" x-model="formData.submitter_email">
            <input type="hidden" name="title" x-model="formData.title">

            <!-- Step 1: Problem -->
            <div x-show="currentStep === 1" x-transition class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    Step 1: Your Problem
                </h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Describe your problem in 4-5 words *
                        </label>
                        <input 
                            type="text" 
                            x-model="formData.problem_short"
                            placeholder='e.g., "Website login is difficult"'
                            maxlength="50"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        >
                        <p class="text-xs text-gray-500 mt-1">Keep it short and clear</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Pain Level (How much does this hurt?) *
                        </label>
                        <div class="flex items-center space-x-4">
                            <input 
                                type="range" 
                                x-model="formData.pain_score"
                                min="0" 
                                max="10" 
                                step="1"
                                class="flex-1"
                            >
                            <div class="w-20 text-center">
                                <span class="text-3xl font-bold" 
                                      :class="{
                                          'text-green-600': formData.pain_score < 4,
                                          'text-yellow-600': formData.pain_score >= 4 && formData.pain_score < 7,
                                          'text-red-600': formData.pain_score >= 7
                                      }"
                                      x-text="formData.pain_score"></span>
                                <p class="text-xs text-gray-600">/10</p>
                            </div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-600 mt-2">
                            <span>😊 No pain</span>
                            <span>😐 Moderate</span>
                            <span>😣 Critical</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="button" @click="nextStep()" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold">
                        Next Step <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Goal -->
            <div x-show="currentStep === 2" x-transition class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-bullseye text-green-500 mr-2"></i>
                    Step 2: Your Goal
                </h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            What needs to change for you to be satisfied? *
                        </label>
                        <textarea 
                            x-model="formData.goal"
                            rows="4"
                            required
                            placeholder="Describe what you want to achieve..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Your Solution Idea (Optional)
                        </label>
                        <textarea 
                            x-model="formData.solution"
                            rows="4"
                            placeholder="If you have ideas on how to solve this, share them here..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        ></textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-between">
                    <button type="button" @click="prevStep()" class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </button>
                    <button type="button" @click="nextStep()" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold">
                        Next Step <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Step 3: Details -->
            <div x-show="currentStep === 3" x-transition class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Step 3: Problem Details
                </h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Describe your problem or pain point in as much detail as possible *
                        </label>
                        <textarea 
                            x-model="formData.description"
                            rows="6"
                            required
                            placeholder="Provide detailed context about the problem..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        ></textarea>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Estimated Cost
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-500">$</span>
                                <input 
                                    type="number" 
                                    x-model="formData.cost_estimate"
                                    placeholder="400.00"
                                    step="0.01"
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Estimated Duration
                            </label>
                            <input 
                                type="text" 
                                x-model="formData.duration_estimate"
                                placeholder="e.g., 3 days, 2 weeks"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Priority Level *
                        </label>
                        <div class="grid grid-cols-4 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" x-model="formData.priority" value="low" class="peer sr-only">
                                <div class="p-3 border-2 border-gray-200 rounded-lg peer-checked:border-gray-500 peer-checked:bg-gray-50 text-center">
                                    <p class="font-semibold text-sm">Low</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" x-model="formData.priority" value="medium" class="peer sr-only">
                                <div class="p-3 border-2 border-blue-200 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 text-center">
                                    <p class="font-semibold text-sm">Medium</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" x-model="formData.priority" value="high" class="peer sr-only">
                                <div class="p-3 border-2 border-orange-200 rounded-lg peer-checked:border-orange-500 peer-checked:bg-orange-50 text-center">
                                    <p class="font-semibold text-sm">High</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" x-model="formData.priority" value="urgent" class="peer sr-only">
                                <div class="p-3 border-2 border-red-200 rounded-lg peer-checked:border-red-500 peer-checked:bg-red-50 text-center">
                                    <p class="font-semibold text-sm">Urgent</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-between">
                    <button type="button" @click="prevStep()" class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </button>
                    <button type="button" @click="nextStep()" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold">
                        Next Step <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Step 4: Review & Submit -->
            <div x-show="currentStep === 4" x-transition class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    Step 4: Review & Submit
                </h2>
                
                <div class="space-y-6">
                    <!-- Summary -->
                    <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Problem</p>
                            <p class="font-semibold" x-text="formData.problem_short"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Pain Level</p>
                            <p class="font-semibold" x-text="formData.pain_score + '/10'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Goal</p>
                            <p class="text-sm" x-text="formData.goal"></p>
                        </div>
                        <div x-show="formData.cost_estimate">
                            <p class="text-xs text-gray-500 mb-1">Estimated Cost</p>
                            <p class="font-semibold" x-text="'$' + formData.cost_estimate"></p>
                        </div>
                        <div x-show="formData.duration_estimate">
                            <p class="text-xs text-gray-500 mb-1">Duration</p>
                            <p class="font-semibold" x-text="formData.duration_estimate"></p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Your Email (for follow-up) *
                        </label>
                        <input 
                            type="email" 
                            x-model="formData.submitter_email"
                            required
                            placeholder="your@email.com"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        >
                    </div>
                </div>

                <div class="mt-8 flex justify-between">
                    <button type="button" @click="prevStep()" class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </button>
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold shadow-lg">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Idea
                    </button>
                </div>
            </div>

        </form>

    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function ideaWizard() {
            return {
                currentStep: 1,
                steps: [
                    { title: 'Problem' },
                    { title: 'Goal' },
                    { title: 'Details' },
                    { title: 'Submit' }
                ],
                formData: {
                    problem_short: '',
                    goal: '',
                    description: '',
                    solution: '',
                    pain_score: 5,
                    cost_estimate: '',
                    duration_estimate: '',
                    priority: 'medium',
                    submitter_email: '{{ Auth::user()->email }}',
                    title: ''
                },
                
                init() {
                    // Auto-set title from problem_short
                    this.$watch('formData.problem_short', value => {
                        this.formData.title = value;
                    });
                },
                
                nextStep() {
                    if (this.validateStep()) {
                        this.currentStep++;
                    }
                },
                
                prevStep() {
                    this.currentStep--;
                },
                
                validateStep() {
                    if (this.currentStep === 1) {
                        if (!this.formData.problem_short) {
                            alert('Please describe your problem');
                            return false;
                        }
                    }
                    if (this.currentStep === 2) {
                        if (!this.formData.goal) {
                            alert('Please describe your goal');
                            return false;
                        }
                    }
                    if (this.currentStep === 3) {
                        if (!this.formData.description) {
                            alert('Please provide problem details');
                            return false;
                        }
                    }
                    return true;
                },
                
                handleSubmit(event) {
                    if (!this.formData.submitter_email) {
                        event.preventDefault();
                        alert('Please provide your email');
                    }
                }
            }
        }
    </script>

</body>
</html>
