<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Idee bearbeiten - {{ $idea->title }} - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50" x-data="{ mobileMenuOpen: false }">

    @include('tenant.partials.nav')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-8 text-white">
            <div class="flex items-center space-x-4 mb-4">
                <a href="{{ route('tenant.ideas.show', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" 
                   class="text-gray-600 hover:text-gray-900 transition">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-edit text-indigo-600 mr-3"></i>
                        Idee bearbeiten
                    </h1>
                    <p class="text-gray-600 mt-2">{{ $idea->problem_short }}</p>
                </div>
            </div>
        </div>

        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4 text-white">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
                <div class="text-sm text-blue-800 text-white">
                    <p class="font-semibold mb-2">Ihre Bearbeitungsrechte ({{ ucfirst($user->role) }})</p>
                    <ul class="space-y-1 text-white">
                        @if($idea->canEditBasic($user))
                            <li><i class="fas fa-check text-green-600 mr-2 text-white"></i>Basis-Infos (Titel, Problem, Ziel, Beschreibung)</li>
                        @endif
                        @if($idea->canEditWorkBee($user))
                            <li><i class="fas fa-check text-green-600 mr-2 text-white"></i>Work-Bee Felder (Schmerz, Umsetzung)</li>
                        @endif
                        @if($idea->canEditDeveloper($user))
                            <li><i class="fas fa-check text-green-600 mr-2 text-white"></i>Entwickler Felder (Lösung, Dauer, Kosten)</li>
                        @endif
                        @if($user->isAdmin())
                            <li><i class="fas fa-check text-green-600 mr-2 text-white"></i>Administrator (Alle Felder + Status)</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('tenant.ideas.update', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}">
            @csrf
            @method('PUT')

            @if($idea->canEditBasic($user))
                <div class="bg-white rounded-xl shadow-lg p-8 mb-6 text-white">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 text-white">
                        <i class="fas fa-file-alt text-indigo-600 mr-2 text-white"></i>
                        Allgemeine Informationen
                    </h2>

                    <div class="space-y-6 text-white">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Titel *</label>
                            <input type="text" name="title" value="{{ old('title', $idea->title) }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Problem (Kurzbeschreibung) *</label>
                            <input type="text" name="problem_short" value="{{ old('problem_short', $idea->problem_short) }}" required maxlength="100"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Zielsetzung *</label>
                            <textarea name="goal" rows="3" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900">{{ old('goal', $idea->goal) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Ausführliche Beschreibung *</label>
                            <textarea name="description" rows="5" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900">{{ old('description', $idea->description) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 text-white">Dringlichkeitsstufe *</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-white">
                                @foreach(['low' => 'Niedrig', 'medium' => 'Mittel', 'high' => 'Hoch', 'urgent' => 'Dringend'] as $key => $label)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="priority" value="{{ $key }}" {{ $idea->priority === $key ? 'checked' : '' }} class="peer sr-only">
                                        <div class="p-3 border-2 rounded-lg peer-checked:border-indigo-500 peer-checked:bg-indigo-50 text-center transition">
                                            <p class="font-semibold text-sm text-gray-700">{{ $label }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 text-white">E-Mail des Einreichers *</label>
                            <input type="email" name="submitter_email" value="{{ old('submitter_email', $idea->submitter_email) }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900">
                        </div>
                    </div>
                </div>
            @endif

            @if($idea->canEditDeveloper($user))
                <div class="bg-white rounded-xl shadow-lg p-8 mb-6 text-white">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 text-white">
                        <i class="fas fa-code text-purple-600 mr-2 text-white"></i>
                        Technische Bewertung (Lösung, Dauer, Kosten)
                    </h2>

                    <div class="space-y-6 text-white">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 text-white">Lösungsvorschlag (Solution)</label>
                            <textarea name="solution" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-gray-900"
                                      placeholder="Beschreiben Sie die technische Umsetzung...">{{ old('solution', $idea->solution) }}</textarea>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4 text-white">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2 text-white">Geschätzte Kosten (€)</label>
                                <div class="relative text-white">
                                    <span class="absolute left-3 top-3 text-gray-500">€</span>
                                    <input type="number" name="cost_estimate" value="{{ old('cost_estimate', $idea->cost_estimate) }}" step="0.01" min="0"
                                           class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-gray-900">
                                </div>
                                <p class="text-xs text-gray-500 mt-1 text-white">Basis für Prio 1: (Kosten / 100) + Dauer</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2 text-white">Geschätzte Dauer (Tage)</label>
                                <input type="text" name="duration_estimate" value="{{ old('duration_estimate', $idea->duration_estimate) }}"
                                       placeholder="z.B. 3 Tage, 2 Wochen"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-gray-900">
                                <p class="text-xs text-gray-500 mt-1 text-white">Wird als numerischer Wert in der Formel genutzt</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($idea->canEditWorkBee($user))
                <div class="bg-white rounded-xl shadow-lg p-8 mb-6 text-white">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 text-white">
                        <i class="fas fa-heartbeat text-green-600 mr-2 text-white"></i>
                        Work-Bee Bewertung (Schmerz, Umsetzung)
                    </h2>

                    <div class="space-y-6 text-white">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 text-white">Schmerzfaktor (Pain Score) *</label>
                            <div class="flex items-center space-x-4 text-white">
                                <input type="range" name="pain_score" value="{{ old('pain_score', $idea->pain_score) }}" 
                                       min="0" max="10" step="1" class="flex-1 accent-indigo-600" 
                                       oninput="this.nextElementSibling.querySelector('span').textContent = this.value">
                                <div class="w-20 text-center text-white">
                                    <span class="text-3xl font-bold text-orange-600">{{ old('pain_score', $idea->pain_score) }}</span>
                                    <p class="text-xs text-gray-600">/10</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 text-white">Basis für Prio 2: Prio 1 / Schmerz</p>
                        </div>

                        <div>
                            <label class="flex items-center space-x-3 text-white">
                                <input type="checkbox" name="in_implementation" value="1" {{ $idea->in_implementation ? 'checked' : '' }}
                                       class="w-5 h-5 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                                <span class="text-sm font-semibold text-gray-700">In Umsetzung (Implementation)</span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 text-white">Umsetzungsdatum</label>
                            <input type="date" name="implementation_date" value="{{ old('implementation_date', $idea->implementation_date?->format('Y-m-d')) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-gray-900">
                        </div>
                    </div>
                </div>
            @endif

            @if($user->isAdmin())
                <div class="bg-white rounded-xl shadow-lg p-8 mb-6 text-white">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 text-white">
                        <i class="fas fa-shield-alt text-red-600 mr-2 text-white"></i>
                        Administrator-Steuerung
                    </h2>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 text-white">Status aktualisieren</label>
                        <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 text-gray-900">
                            @foreach(['pending' => 'Offen (Awaiting Review)', 'in-review' => 'In Prüfung', 'approved' => 'Genehmigt', 'rejected' => 'Abgelehnt', 'implemented' => 'Umgesetzt'] as $status => $label)
                                <option value="{{ $status }}" {{ $idea->status === $status ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl shadow-lg p-8 mb-6 border border-indigo-200 text-white">
                <h2 class="text-xl font-bold text-gray-900 mb-4 text-white">
                    <i class="fas fa-calculator text-indigo-600 mr-2 text-white"></i>
                    Automatisch berechnete Prioritäten
                </h2>
                <div class="grid md:grid-cols-2 gap-6 text-white">
                    <div class="bg-white rounded-lg p-4 text-white">
                        <p class="text-sm text-gray-600 mb-2">Prio 1 = (Kosten / 100) + Dauer</p>
                        <p class="text-3xl font-bold text-indigo-600">{{ number_format($idea->priority_1, 2, ',', '.') }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 text-white">
                        <p class="text-sm text-gray-600 mb-2">Prio 2 = Prio 1 / Schmerz</p>
                        <p class="text-3xl font-bold text-purple-600">{{ number_format($idea->priority_2, 2, ',', '.') }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-4 text-white">
                    <i class="fas fa-info-circle mr-1 text-white"></i>
                    Diese Werte werden beim Speichern basierend auf den obigen Angaben neu berechnet.
                </p>
            </div>

            <div class="flex items-center justify-between text-white">
                <a href="{{ route('tenant.ideas.show', ['tenantId' => $tenant->id, 'idea' => $idea->id]) }}" 
                   class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition font-medium text-white">
                    <i class="fas fa-times mr-2 text-white"></i>Abbrechen
                </a>
                <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg transform hover:scale-105">
                    <i class="fas fa-save mr-2 text-white"></i>Änderungen speichern
                </button>
            </div>

        </form>

    </div>

</body>
</html>