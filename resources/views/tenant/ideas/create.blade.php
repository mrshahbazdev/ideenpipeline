<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neue Idee einreichen - {{ $currentTeam->name }} - {{ $tenant->subdomain }}</title>
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
        
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <a href="{{ route('tenant.ideas.index', ['tenantId' => $tenant->id]) }}" 
                   class="text-gray-600 hover:text-gray-900 transition">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-lightbulb text-yellow-500 mr-3"></i>
                        Neue Idee einreichen
                    </h1>
                    <p class="text-gray-600 mt-2">Beschreiben Sie Ihr Problem und Ihre Lösungsidee</p>
                </div>
            </div>
        </div>

        <div class="mb-8 bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="step-item flex-1 text-center"
                         :class="{
                             'step-active': currentStep === index + 1,
                             'step-completed': currentStep > index + 1
                         }">
                        <div class="step-circle w-16 h-16 mx-auto rounded-full border-4 border-gray-300 flex items-center justify-center font-bold text-lg bg-white mb-2 transition-all">
                            <span x-show="currentStep <= index + 1" x-text="index + 1"></span>
                            <i x-show="currentStep > index + 1" class="fas fa-check"></i>
                        </div>
                        <p class="text-xs md:text-sm font-medium" x-text="step.title"></p>
                    </div>
                </template>
            </div>
        </div>

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

            <div x-show="currentStep === 1" x-transition class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    Schritt 1: Das Problem
                </h2>
                
                <div class="space-y-6 text-white">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Beschreiben Sie das Problem in 4-5 Worten *
                        </label>
                        <input 
                            type="text" 
                            x-model="formData.problem_short"
                            placeholder='z.B. "Login auf der Webseite zu langsam"'
                            maxlength="50"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900"
                        >
                        <p class="text-xs text-gray-500 mt-1">Halten Sie es kurz und prägnant</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Schmerzfaktor (Wie sehr stört dieses Problem?) *
                        </label>
                        <div class="flex items-center space-x-4">
                            <input 
                                type="range" 
                                x-model="formData.pain_score"
                                min="0" 
                                max="10" 
                                step="1"
                                class="flex-1 accent-indigo-600"
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
                            <span>😊 Kaum spürbar</span>
                            <span>😐 Moderat</span>
                            <span>😣 Kritisch</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="button" @click="nextStep()" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition">
                        Nächster Schritt <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <div x-show="currentStep === 2" x-transition class="bg-white rounded-xl shadow-lg p-8" style="display: none;">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-bullseye text-green-500 mr-2"></i>
                    Schritt 2: Das Ziel
                </h2>
                
                <div class="space-y-6 text-white">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Was muss sich ändern, damit Sie zufrieden sind? *
                        </label>
                        <textarea 
                            x-model="formData.goal"
                            rows="4"
                            required
                            placeholder="Beschreiben Sie, was Sie erreichen möchten..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Ihr Lösungsvorschlag (Optional)
                        </label>
                        <textarea 
                            x-model="formData.solution"
                            rows="4"
                            placeholder="Wenn Sie bereits eine Idee zur Umsetzung haben, teilen Sie uns diese mit..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900"
                        ></textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-between">
                    <button type="button" @click="prevStep()" class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-arrow-left mr-2"></i>Zurück
                    </button>
                    <button type="button" @click="nextStep()" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition">
                        Nächster Schritt <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <div x-show="currentStep === 3" x-transition class="bg-white rounded-xl shadow-lg p-8" style="display: none;">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Schritt 3: Details zum Problem
                </h2>
                
                <div class="space-y-6 text-white">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Beschreiben Sie das Problem so detailliert wie möglich *
                        </label>
                        <textarea 
                            x-model="formData.description"
                            rows="6"
                            required
                            placeholder="Geben Sie hier alle wichtigen Details und den Kontext an..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900"
                        ></textarea>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 text-white">
                                Geschätzte Kosten (falls bekannt)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-500">€</span>
                                <input 
                                    type="number" 
                                    x-model="formData.cost_estimate"
                                    placeholder="400.00"
                                    step="0.01"
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 text-white">
                                Geschätzte Dauer
                            </label>
                            <input 
                                type="text" 
                                x-model="formData.duration_estimate"
                                placeholder="z.B. 3 Tage, 2 Wochen"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 text-white">
                            Priorität (Ihre Einschätzung) *
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-white">
                            <label class="cursor-pointer">
                                <input type="radio" x-model="formData.priority" value="low" class="peer sr-only">
                                <div class="p-3 border-2 border-gray-200 rounded-lg peer-checked:border-gray-500 peer-checked:bg-gray-50 text-center">
                                    <p class="font-semibold text-sm text-gray-700">Niedrig</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" x-model="formData.priority" value="medium" class="peer sr-only">
                                <div class="p-3 border-2 border-blue-200 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 text-center">
                                    <p class="font-semibold text-sm text-gray-700">Mittel</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" x-model="formData.priority" value="high" class="peer sr-only">
                                <div class="p-3 border-2 border-orange-200 rounded-lg peer-checked:border-orange-500 peer-checked:bg-orange-50 text-center">
                                    <p class="font-semibold text-sm text-gray-700">Hoch</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" x-model="formData.priority" value="urgent" class="peer sr-only">
                                <div class="p-3 border-2 border-red-200 rounded-lg peer-checked:border-red-500 peer-checked:bg-red-50 text-center">
                                    <p class="font-semibold text-sm text-gray-700">Dringend</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-between">
                    <button type="button" @click="prevStep()" class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-arrow-left mr-2"></i>Zurück
                    </button>
                    <button type="button" @click="nextStep()" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition">
                        Nächster Schritt <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <div x-show="currentStep === 4" x-transition class="bg-white rounded-xl shadow-lg p-8" style="display: none;">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    Schritt 4: Zusammenfassung & Absenden
                </h2>
                
                <div class="space-y-6 text-white">
                    <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1 uppercase font-bold">Problem</p>
                            <p class="font-semibold text-gray-900" x-text="formData.problem_short"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1 uppercase font-bold">Schmerzfaktor</p>
                            <p class="font-semibold text-gray-900" x-text="formData.pain_score + '/10'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1 uppercase font-bold">Ziel</p>
                            <p class="text-sm text-gray-800" x-text="formData.goal"></p>
                        </div>
                        <div x-show="formData.cost_estimate">
                            <p class="text-xs text-gray-500 mb-1 uppercase font-bold">Geschätzte Kosten</p>
                            <p class="font-semibold text-gray-900" x-text="formData.cost_estimate + ' €'"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Ihre E-Mail (für Rückfragen) *
                        </label>
                        <input 
                            type="email" 
                            x-model="formData.submitter_email"
                            required
                            placeholder="beispiel@email.de"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900"
                        >
                    </div>
                </div>

                <div class="mt-8 flex justify-between">
                    <button type="button" @click="prevStep()" class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-arrow-left mr-2"></i>Zurück
                    </button>
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold shadow-lg transition">
                        <i class="fas fa-paper-plane mr-2 text-white"></i>Idee jetzt einreichen
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
                    { title: 'Ziel' },
                    { title: 'Details' },
                    { title: 'Absenden' }
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
                            alert('Bitte beschreiben Sie Ihr Problem kurz.');
                            return false;
                        }
                    }
                    if (this.currentStep === 2) {
                        if (!this.formData.goal) {
                            alert('Bitte beschreiben Sie das gewünschte Ziel.');
                            return false;
                        }
                    }
                    if (this.currentStep === 3) {
                        if (!this.formData.description) {
                            alert('Bitte geben Sie Details zum Problem an.');
                            return false;
                        }
                    }
                    return true;
                },
                
                handleSubmit(event) {
                    if (!this.formData.submitter_email) {
                        event.preventDefault();
                        alert('Bitte geben Sie Ihre E-Mail-Adresse an.');
                    }
                }
            }
        }
    </script>

</body>
</html>