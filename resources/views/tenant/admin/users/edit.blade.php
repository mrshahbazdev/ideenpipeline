<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzer bearbeiten - {{ $user->name }} - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50" x-data="{ mobileMenuOpen: false, showPassword: false, selectedRole: '{{ $user->role }}' }">

    @include('tenant.partials.nav')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-white">
        
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <a href="{{ route('tenant.admin.users.index', ['tenantId' => $tenant->id]) }}" 
                   class="text-gray-600 hover:text-gray-900 transition">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div class="flex-1">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg {{ $user->role_color }}">
                            {{ $user->initials }}
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">
                                Benutzer bearbeiten: {{ $user->name }}
                            </h1>
                            <p class="text-gray-600 mt-1">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-2 text-sm text-gray-600 ml-14">
                <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" class="hover:text-indigo-600">Dashboard</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <a href="{{ route('tenant.admin.users.index', ['tenantId' => $tenant->id]) }}" class="hover:text-indigo-600">Benutzer</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900 font-medium">Bearbeiten</span>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mb-8 text-white">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <p class="text-xs text-gray-600 mb-1 uppercase font-bold text-white">Teams</p>
                <p class="text-2xl font-bold text-blue-600">{{ $user->teams()->count() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                <p class="text-xs text-gray-600 mb-1 uppercase font-bold text-white">Eingereichte Ideen</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $user->ideas()->count() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500 text-white">
                <p class="text-xs text-gray-600 mb-1 uppercase font-bold text-white">Abgegebene Stimmen</p>
                <p class="text-2xl font-bold text-green-600">{{ $user->votes()->count() }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('tenant.admin.users.update', ['tenantId' => $tenant->id, 'user' => $user->id]) }}">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl shadow-lg p-8 mb-6 text-white">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-id-card text-indigo-600 mr-2 text-white"></i>
                    Basis-Informationen
                </h2>

                <div class="space-y-6 text-white">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2 text-white">
                            <i class="fas fa-user text-gray-400 mr-2 text-white"></i>Vollständiger Name *
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition @error('name') border-red-500 @enderror text-gray-900"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope text-gray-400 mr-2"></i>E-Mail-Adresse *
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition @error('email') border-red-500 @enderror text-gray-900"
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock text-gray-400 mr-2 text-white"></i>Neues Passwort (Optional)
                        </label>
                        <div class="relative text-white">
                            <input 
                                :type="showPassword ? 'text' : 'password'" 
                                name="password" 
                                id="password"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition @error('password') border-red-500 @enderror text-gray-900"
                                placeholder="Leer lassen, um aktuelles Passwort zu behalten"
                            >
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 text-white">
                            <i class="fas fa-info-circle mr-1 text-white"></i>
                            Nur ausfüllen, wenn Sie das Passwort ändern möchten
                        </p>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1 text-white"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8 mb-6 text-white">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-user-shield text-indigo-600 mr-2 text-white"></i>
                    Rolle & Berechtigungen
                </h2>

                <div class="space-y-4 text-white">
                    <div class="grid md:grid-cols-2 gap-4 text-white">
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="admin" x-model="selectedRole" 
                                   {{ old('role', $user->role) === 'admin' ? 'checked' : '' }}
                                   class="peer sr-only">
                            <div class="p-6 border-2 border-gray-200 rounded-xl peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-300 transition h-full text-white">
                                <div class="flex items-center justify-between mb-3 text-white">
                                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-lg flex items-center justify-center shadow-lg text-white">
                                        <i class="fas fa-crown text-white text-xl"></i>
                                    </div>
                                    <span class="hidden peer-checked:inline-flex w-6 h-6 bg-red-500 text-white rounded-full items-center justify-center text-white">
                                        <i class="fas fa-check text-xs"></i>
                                    </span>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-2">Admin</h3>
                                <p class="text-xs text-gray-600">Voller Systemzugriff und Verwaltung</p>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="developer" x-model="selectedRole"
                                   {{ old('role', $user->role) === 'developer' ? 'checked' : '' }}
                                   class="peer sr-only text-white">
                            <div class="p-6 border-2 border-gray-200 rounded-xl peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-purple-300 transition h-full text-white">
                                <div class="flex items-center justify-between mb-3 text-white">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg text-white">
                                        <i class="fas fa-code text-white text-xl text-white"></i>
                                    </div>
                                    <span class="hidden peer-checked:inline-flex w-6 h-6 bg-purple-500 text-white rounded-full items-center justify-center">
                                        <i class="fas fa-check text-xs"></i>
                                    </span>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-2">Entwickler</h3>
                                <p class="text-xs text-gray-600">Technische Berechtigungen und Lösungsverwaltung</p>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="work-bee" x-model="selectedRole"
                                   {{ old('role', $user->role) === 'work-bee' ? 'checked' : '' }}
                                   class="peer sr-only">
                            <div class="p-6 border-2 border-gray-200 rounded-xl peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300 transition h-full">
                                <div class="flex items-center justify-between mb-3 text-white">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-lg text-white">
                                        <i class="fas fa-user-friends text-white text-xl"></i>
                                    </div>
                                    <span class="hidden peer-checked:inline-flex w-6 h-6 bg-green-500 text-white rounded-full items-center justify-center text-white">
                                        <i class="fas fa-check text-xs"></i>
                                    </span>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-2">Work-Bee</h3>
                                <p class="text-xs text-gray-600">Bewertung von Schmerzfaktor & Umsetzung</p>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="standard" x-model="selectedRole"
                                   {{ old('role', $user->role) === 'standard' ? 'checked' : '' }}
                                   class="peer sr-only">
                            <div class="p-6 border-2 border-gray-200 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300 transition h-full">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center shadow-lg text-white">
                                        <i class="fas fa-user text-white text-xl text-white"></i>
                                    </div>
                                    <span class="hidden peer-checked:inline-flex w-6 h-6 bg-blue-500 text-white rounded-full items-center justify-center">
                                        <i class="fas fa-check text-xs"></i>
                                    </span>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-2">Standard</h3>
                                <p class="text-xs text-gray-600">Grundlegende Berechtigungen (Ideen einreichen)</p>
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <p class="mt-2 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8 mb-6 text-white">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center text-white">
                    <i class="fas fa-toggle-on text-indigo-600 mr-2 text-white"></i>
                    Kontostatus
                </h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3 text-white">
                            <input 
                                type="checkbox" 
                                name="is_active" 
                                id="is_active"
                                value="1"
                                {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                class="w-5 h-5 text-green-600 rounded focus:ring-2 focus:ring-green-500"
                            >
                            <label for="is_active" class="text-sm font-medium text-gray-700">
                                Benutzerkonto ist aktiv
                            </label>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            <i class="fas fa-circle text-[8px] mr-1 {{ $user->is_active ? 'text-green-500' : 'text-gray-400' }}"></i>
                            {{ $user->is_active ? 'AKTIV' : 'INAKTIV' }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 ml-8 text-white">
                        <i class="fas fa-info-circle mr-1 text-white"></i>
                        Inaktive Benutzer können sich nicht am System anmelden.
                    </p>
                </div>
            </div>

            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-lg p-8 mb-6 border border-blue-200 text-white">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    System-Informationen
                </h2>

                <div class="grid md:grid-cols-2 gap-6 text-white">
                    <div>
                        <p class="text-xs text-gray-600 mb-1 uppercase font-bold text-white">Mitglied seit</p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $user->created_at->format('d. F Y') }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-600 mb-1 uppercase font-bold text-white">Zuletzt aktualisiert</p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $user->updated_at->format('d. F Y') }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $user->updated_at->diffForHumans() }}</p>
                    </div>

                    @if($user->last_login_at)
                        <div>
                            <p class="text-xs text-gray-600 mb-1 uppercase font-bold text-white">Letzter Login</p>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $user->last_login_at->format('d.m.Y H:i') }} Uhr
                            </p>
                            <p class="text-xs text-gray-500">{{ $user->last_login_at->diffForHumans() }}</p>
                        </div>
                    @endif

                    <div>
                        <p class="text-xs text-gray-600 mb-1 uppercase font-bold">Benutzer-ID</p>
                        <p class="text-sm font-mono font-semibold text-gray-900">{{ $user->id }}</p>
                    </div>
                </div>
            </div>

            @if($user->id === Auth::id())
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-lg mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3 mt-1"></i>
                        <div>
                            <p class="text-sm font-bold text-yellow-900 mb-1">Sie bearbeiten Ihr eigenes Konto</p>
                            <p class="text-xs text-yellow-800">
                                Seien Sie vorsichtig beim Ändern Ihrer Rolle oder beim Deaktivieren Ihres Kontos, da dies Ihren eigenen Zugriff auf das System beeinträchtigen kann.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-between text-white">
                <a href="{{ route('tenant.admin.users.index', ['tenantId' => $tenant->id]) }}" 
                   class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition font-semibold text-white">
                    <i class="fas fa-times mr-2 text-white"></i>Abbrechen
                </a>
                <div class="flex items-center space-x-3 text-white">
                    @if($user->id !== Auth::id())
                        <button 
                            type="button"
                            onclick="if(confirm('Sind Sie sicher, dass Sie diesen Benutzer löschen möchten?')) { document.getElementById('delete-form').submit(); }"
                            class="px-6 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition shadow-md">
                            <i class="fas fa-trash mr-2 text-white"></i>Benutzer löschen
                        </button>
                    @endif
                    <button 
                        type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-semibold hover:from-indigo-700 hover:to-purple-700 transition shadow-lg transform hover:scale-105">
                        <i class="fas fa-save mr-2 text-white"></i>Änderungen speichern
                    </button>
                </div>
            </div>

        </form>

        @if($user->id !== Auth::id())
            <form id="delete-form" method="POST" action="{{ route('tenant.admin.users.destroy', ['tenantId' => $tenant->id, 'user' => $user->id]) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif

    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>