<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team erstellen - {{ $tenant->subdomain }}</title>
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

    <nav class="bg-white shadow-sm sticky top-0 z-50 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" class="text-xl font-bold text-indigo-600">
                        <i class="fas fa-building mr-2 text-white"></i>{{ $tenant->subdomain }}
                    </a>
                    <span class="ml-3 px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full text-white">
                        <i class="fas fa-crown mr-1"></i>ADMIN
                    </span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('tenant.teams.index', ['tenantId' => $tenant->id]) }}" class="text-gray-600 hover:text-gray-900 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>Zurück zur Übersicht
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-users-cog text-indigo-600 mr-3"></i>
                Neues Team erstellen
            </h1>
            <p class="text-gray-600 mt-2">Organisieren Sie Ihre Teammitglieder in Gruppen für eine bessere Zusammenarbeit.</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <form method="POST" action="{{ route('tenant.teams.store', ['tenantId' => $tenant->id]) }}" id="createTeamForm">
                @csrf

                <div class="p-8 space-y-6">

                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tag text-gray-400 mr-2"></i>Team-Name *
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name"
                            value="{{ old('name') }}"
                            required
                            maxlength="255"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('name') border-red-500 @enderror text-gray-900"
                            placeholder="z.B. Entwicklungsteam, Marketing-Abteilung"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>Wählen Sie einen aussagekräftigen Namen für Ihr Team
                        </p>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-align-left text-gray-400 mr-2"></i>Beschreibung
                        </label>
                        <textarea 
                            name="description" 
                            id="description"
                            rows="4"
                            maxlength="1000"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('description') border-red-500 @enderror text-gray-900"
                            placeholder="Beschreiben Sie den Zweck und die Verantwortlichkeiten des Teams..."
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <span id="charCount">0</span> / 1000 Zeichen
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-palette text-gray-400 mr-2"></i>Team-Farbe *
                        </label>
                        <input type="hidden" name="color" id="colorInput" value="{{ old('color', '#3B82F6') }}">
                        
                        <div class="grid grid-cols-8 md:grid-cols-12 gap-3 mb-4">
                            @php
                                $colors = [
                                    '#3B82F6' => 'Blau',
                                    '#10B981' => 'Grün',
                                    '#EF4444' => 'Rot',
                                    '#F59E0B' => 'Orange',
                                    '#8B5CF6' => 'Lila',
                                    '#EC4899' => 'Pink',
                                    '#14B8A6' => 'Türkis',
                                    '#F97316' => 'Orange',
                                    '#6366F1' => 'Indigo',
                                    '#06B6D4' => 'Cyan',
                                    '#84CC16' => 'Limette',
                                    '#A855F7' => 'Violett',
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

                        <div class="flex items-center space-x-3 text-white">
                            <label class="text-sm text-gray-600">Oder eigene Farbe wählen:</label>
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

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-users text-gray-400 mr-2"></i>Teammitglieder
                        </label>
                        
                        @if($availableUsers->count() > 0)
                            <div class="mb-4">
                                <input 
                                    type="text" 
                                    id="memberSearch"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                                    placeholder="Mitglieder suchen..."
                                    onkeyup="filterMembers()"
                                >
                            </div>

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
                                                <label for="member_{{ $user->id }}" class="ml-3 flex-1 flex items-center cursor-pointer text-white">
                                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold
                                                        {{ $user->role === 'developer' ? 'bg-purple-500' : '' }}
                                                        {{ $user->role === 'work-bee' ? 'bg-green-500' : '' }}
                                                        {{ $user->role === 'standard' ? 'bg-blue-500' : '' }}
                                                    ">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                    
                                                    <div class="ml-3 flex-1 text-white">
                                                        <p class="text-sm font-medium text-gray-900 member-name">{{ $user->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                                    </div>

                                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $user->getRoleBadgeClass() }}">
                                                        <i class="fas {{ $user->getRoleIcon() }} mr-1"></i>
                                                        {{ $user->role === 'developer' ? 'Entwickler' : ucfirst(str_replace('-', ' ', $user->role)) }}
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <p class="mt-3 text-sm text-gray-600">
                                <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                <span id="selectedCount">{{ count(old('members', [])) }}</span> Mitglied(er) ausgewählt
                            </p>

                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
                                <i class="fas fa-users text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-600">Keine Benutzer zum Hinzufügen verfügbar</p>
                                <p class="text-sm text-gray-500 mt-1 text-white">Laden Sie zuerst Teammitglieder ein</p>
                            </div>
                        @endif

                        @error('members')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <a href="{{ route('tenant.teams.index', ['tenantId' => $tenant->id]) }}" class="px-6 py-2 text-gray-700 hover:bg-gray-200 rounded-lg transition font-medium">
                        <i class="fas fa-times mr-2"></i>Abbrechen
                    </a>
                    <button 
                        type="submit"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition transform hover:scale-105 shadow-md"
                    >
                        <i class="fas fa-check mr-2"></i>Team erstellen
                    </button>
                </div>

            </form>
        </div>

        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex text-white">
                <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
                <div class="text-white">
                    <h4 class="font-semibold text-blue-900 mb-2">Tipps zur Teamerstellung:</h4>
                    <ul class="text-sm text-blue-800 space-y-1 text-white">
                        <li><i class="fas fa-check text-blue-500 mr-2"></i>Wählen Sie einen klaren und beschreibenden Teamnamen</li>
                        <li><i class="fas fa-check text-blue-500 mr-2"></i>Fügen Sie eine Beschreibung hinzu, um den Zweck des Teams zu klären</li>
                        <li><i class="fas fa-check text-blue-500 mr-2"></i>Wählen Sie eine markante Farbe zur einfachen Identifizierung</li>
                        <li><i class="fas fa-check text-blue-500 mr-2"></i>Mitglieder können jetzt oder später über die Team-Detailseite hinzugefügt werden</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Farbauswahl
        function selectColor(color) {
            document.getElementById('colorInput').value = color;
            
            // Markierungsstatus aktualisieren
            document.querySelectorAll('.color-option').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Da style.backgroundColor HEX oft in RGB umwandelt, suchen wir über den Style
            document.querySelectorAll('.color-option').forEach(el => {
                if(el.style.backgroundColor.toLowerCase() === color.toLowerCase() || 
                   rgbToHex(el.style.backgroundColor).toLowerCase() === color.toLowerCase()) {
                    el.classList.add('selected');
                }
            });
        }

        // Hilfsfunktion für HEX Vergleich
        function rgbToHex(rgb) {
            if(!rgb || !rgb.startsWith('rgb')) return rgb;
            let sep = rgb.indexOf(",") > -1 ? "," : " ";
            rgb = rgb.substr(4).split(")")[0].split(sep);
            let r = (+rgb[0]).toString(16),
                g = (+rgb[1]).toString(16),
                b = (+rgb[2]).toString(16);
            if (r.length == 1) r = "0" + r;
            if (g.length == 1) g = "0" + g;
            if (b.length == 1) b = "0" + b;
            return "#" + r + g + b;
        }

        // Zeichenzähler für Beschreibung
        const descriptionInput = document.getElementById('description');
        const charCount = document.getElementById('charCount');
        
        if (descriptionInput) {
            descriptionInput.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
            charCount.textContent = descriptionInput.value.length;
        }

        // Counter für Mitgliederauswahl
        const checkboxes = document.querySelectorAll('.member-checkbox');
        const selectedCount = document.getElementById('selectedCount');
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });
        
        function updateSelectedCount() {
            const checked = document.querySelectorAll('.member-checkbox:checked').length;
            selectedCount.textContent = checked;
        }

        // Filter für Mitgliedersuche
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

        // Formular-Validierung
        document.getElementById('createTeamForm').addEventListener('submit', function(e) {
            const teamName = document.getElementById('name').value.trim();
            
            if (teamName.length < 3) {
                e.preventDefault();
                alert('Der Teamname muss mindestens 3 Zeichen lang sein.');
                document.getElementById('name').focus();
                return false;
            }
        });
    </script>

</body>
</html>