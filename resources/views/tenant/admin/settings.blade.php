<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Einstellungen - {{ $tenant->subdomain }}</title>
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

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 fade-in text-white">
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-cog text-indigo-600 mr-3"></i>
                Mandanten-Einstellungen
            </h1>
            <p class="text-gray-600 mt-2">Konfigurieren Sie die allgemeinen Einstellungen Ihrer Organisation</p>

            <div class="flex items-center space-x-2 text-sm text-gray-600 mt-4 text-white">
                <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" 
                   class="hover:text-indigo-600 transition-colors">
                    Dashboard
                </a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900 font-medium">Einstellungen</span>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm" 
                 x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)">
                <div class="flex items-center justify-between text-white">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3 text-white"></i>
                        <p class="text-green-800 font-medium text-white">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-green-600 hover:text-green-800 text-white">
                        <i class="fas fa-times text-white"></i>
                    </button>
                </div>
            </div>
        @endif

        <form method="POST" 
              action="{{ route('tenant.admin.settings.update', ['tenantId' => $tenant->id]) }}" 
              id="settingsForm"
              x-data="{
                  subdomain: '{{ $tenant->subdomain }}',
                  currentStatus: '{{ $tenant->status }}',
                  originalSubdomain: '{{ $tenant->subdomain }}',
                  originalStatus: '{{ $tenant->status }}',
                  isSubmitting: false
              }"
              @submit.prevent="
                  if(isSubmitting) return false;
                  
                  if(subdomain !== originalSubdomain) {
                      if(!confirm('⚠️ Das Ändern der Subdomain ändert die URL Ihrer Organisation. Bestehende Links werden ungültig. Möchten Sie wirklich fortfahren?')) {
                          return false;
                      }
                  }
                  
                  if(currentStatus !== originalStatus && (currentStatus === 'suspended' || currentStatus === 'expired')) {
                      if(!confirm('⚠️ Das Deaktivieren des Kontos betrifft den Zugriff ALLER Benutzer. Fortfahren?')) {
                          return false;
                      }
                  }
                  
                  isSubmitting = true;
                  $el.submit();
              ">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl shadow-lg p-8 mb-6 border border-gray-200 text-white">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center text-white">
                    <i class="fas fa-building text-indigo-600 mr-2 text-white"></i>
                    Organisations-Informationen
                </h2>

                <div class="space-y-6 text-white">
                    <div>
                        <label for="subdomain" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-link text-gray-400 mr-2"></i>Subdomain *
                        </label>
                        <div class="flex items-center text-white">
                            <input 
                                type="text" 
                                name="subdomain" 
                                id="subdomain"
                                value="{{ old('subdomain', $tenant->subdomain) }}"
                                required
                                pattern='[a-zA-Z0-9-]+'
                                title='Nur Buchstaben, Zahlen und Bindestriche sind erlaubt'
                                maxlength="63"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('subdomain') border-red-500 @enderror text-gray-900"
                                placeholder="ihre-firma"
                                x-model="subdomain"
                            >
                            <span class="px-4 py-3 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-gray-600 font-mono">
                                .ideenpipeline.de
                            </span>
                        </div>
                        <div class="flex items-center justify-between mt-2 text-white">
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Die eindeutige Kennung Ihrer Organisation. Eine Änderung beeinflusst Ihre Zugriffs-URL.
                            </p>
                            <span class="text-xs text-indigo-600 font-medium" x-text="subdomain + '.ideenpipeline.de'"></span>
                        </div>
                        @error('subdomain')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 text-white">
                            <i class="fas fa-fingerprint text-gray-400 mr-2 text-white"></i>Mandanten-ID (Tenant ID)
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
                                    @click="navigator.clipboard.writeText('{{ $tenant->id }}'); alert('ID in Zwischenablage kopiert!')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-lock mr-1"></i>Dies ist Ihre interne System-ID und kann nicht geändert werden.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8 mb-6 border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-toggle-on text-indigo-600 mr-2"></i>
                    Status & Abonnement
                </h2>

                <div class="space-y-6">
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-circle text-gray-400 mr-2"></i>Konto-Status *
                        </label>
                        <select 
                            name="status" 
                            id="status"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-gray-900"
                            x-model="currentStatus"
                        >
                            <option value="active">✅ Aktiv - Voller Zugriff</option>
                            <option value="suspended">⏸️ Suspendiert - Vorübergehend deaktiviert</option>
                            <option value="expired">⏰ Abgelaufen - Abonnement beendet</option>
                        </select>
                    </div>

                    <div class="p-4 rounded-lg transition-colors" 
                         :class="{
                             'status-active': currentStatus === 'active',
                             'status-suspended': currentStatus === 'suspended',
                             'status-expired': currentStatus === 'expired'
                         }">
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
                                Aktueller Status: <span x-text="currentStatus === 'active' ? 'Aktiv' : (currentStatus === 'suspended' ? 'Suspendiert' : 'Abgelaufen')"></span>
                            </p>
                        </div>
                    </div>

                    <div>
                        <label for="expires_at" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar text-gray-400 mr-2"></i>Abonnement läuft ab am
                        </label>
                        <input 
                            type="date" 
                            name="expires_at" 
                            id="expires_at"
                            value="{{ old('expires_at', $tenant->expires_at ? $tenant->expires_at->format('Y-m-d') : '') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                        >
                        <div class="flex items-center justify-between mt-2 text-white">
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Leer lassen für ein unbegrenztes Abonnement.
                            </p>
                            @if($tenant->expires_at)
                                <span class="text-xs {{ $tenant->expires_at->isPast() ? 'text-red-500' : 'text-green-500' }}">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $tenant->expires_at->isPast() ? 'Abgelaufen ' : 'Läuft ab ' }}{{ $tenant->expires_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-lg p-8 mb-6 border border-blue-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    System-Informationen
                </h2>

                <div class="grid md:grid-cols-2 gap-6 text-white">
                    <div class="bg-white/80 p-4 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1 flex items-center">
                            <i class="fas fa-calendar-plus mr-2 text-white"></i>Erstellt am
                        </p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $tenant->created_at->format('d. F Y') }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $tenant->created_at->diffForHumans() }}</p>
                    </div>

                    <div class="bg-white/80 p-4 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1 flex items-center text-white">
                            <i class="fas fa-history mr-2"></i>Zuletzt aktualisiert
                        </p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $tenant->updated_at->format('d. F Y') }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $tenant->updated_at->diffForHumans() }}</p>
                    </div>

                    <div class="bg-white/80 p-4 rounded-lg md:col-span-2">
                        <p class="text-xs text-gray-600 mb-1 flex items-center text-white">
                            <i class="fas fa-external-link-alt mr-2 text-white"></i>Zugriffs-URL
                        </p>
                        <a href="https://{{ $tenant->subdomain }}.ideenpipeline.de" 
                           target="_blank"
                           class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center">
                            <span>{{ $tenant->subdomain }}.ideenpipeline.de</span>
                            <i class="fas fa-external-link-alt text-xs ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-lg mb-6">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3 mt-1"></i>
                    <div>
                        <p class="text-sm font-bold text-yellow-900 mb-1">Wichtiger Hinweis</p>
                        <p class="text-xs text-yellow-800">
                            Änderungen an der Subdomain oder dem Status wirken sich auf alle Benutzer aus. Stellen Sie sicher, dass Sie die Auswirkungen verstehen, bevor Sie speichern.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-white">
                <div class="flex space-x-3 text-white">
                    <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" 
                       class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition font-semibold flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>Zum Dashboard
                    </a>
                    
                    <button type="reset" 
                            onclick="if(confirm('Möchten Sie wirklich alle Änderungen auf die ursprünglichen Werte zurücksetzen?')) { window.location.reload(); }"
                            class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition font-semibold flex items-center">
                        <i class="fas fa-undo mr-2"></i>Zurücksetzen
                    </button>
                </div>
                
                <button 
                    type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-semibold hover:from-indigo-700 hover:to-purple-700 transition shadow-lg transform hover:scale-105 disabled:opacity-50"
                    :disabled="isSubmitting">
                    <i class="fas fa-save mr-2"></i>
                    <span x-text="isSubmitting ? 'Wird gespeichert...' : 'Einstellungen speichern'"></span>
                </button>
            </div>
        </form>
    </div>
</body>
</html>