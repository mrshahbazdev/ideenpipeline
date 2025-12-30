<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team bearbeiten: {{ $team->name }} - {{ $tenant->subdomain }}</title>
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
<body class="bg-gray-50 text-white">

    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" class="text-xl font-bold text-indigo-600">
                        <i class="fas fa-building mr-2"></i>{{ $tenant->subdomain }}
                    </a>
                    <span class="ml-3 px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full text-white">
                        <i class="fas fa-crown mr-1"></i>ADMIN
                    </span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('tenant.teams.show', ['tenantId' => $tenant->id, 'team' => $team->id]) }}" class="text-gray-600 hover:text-gray-900 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>Zurück zum Team
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-edit text-indigo-600 mr-3"></i>
                Team bearbeiten
            </h1>
            <p class="text-gray-600 mt-2 text-white">Team-Informationen und Einstellungen aktualisieren</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <form method="POST" action="{{ route('tenant.teams.update', ['tenantId' => $tenant->id, 'team' => $team->id]) }}" id="editTeamForm">
                @csrf
                @method('PUT')

                <div class="p-8 space-y-6">

                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6 border border-gray-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white font-bold text-2xl shadow-lg" 
                                 style="background: {{ $team->color }}" id="teamPreviewIcon">
                                {{ strtoupper(substr($team->name, 0, 1)) }}
                            </div>
                            <div class="text-white">
                                <p class="text-sm text-gray-600 mb-1">Aktuelles Team</p>
                                <h3 class="text-xl font-bold text-gray-900" id="teamPreviewName">{{ $team->name }}</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    <i class="fas fa-users mr-1"></i>{{ $team->members->count() }} Mitglieder
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tag text-gray-400 mr-2"></i>Teamname *
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name"
                            value="{{ old('name', $team->name) }}"
                            required
                            maxlength="255"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('name') border-red-500 @enderror text-gray-900"
                            placeholder="z.B. Entwicklungsteam, Marketing-Abteilung"
                            oninput="updatePreview()"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
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
                            placeholder="Beschreiben Sie den Zweck und die Aufgaben des Teams..."
                        >{{ old('description', $team->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1 text-white"></i>{{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <span id="charCount">{{ strlen($team->description) }}</span> / 1000 Zeichen
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-palette text-gray-400 mr-2"></i>Team-Farbe *
                        </label>
                        <input type="hidden" name="color" id="colorInput" value="{{ old('color', $team->color) }}">
                        
                        <div class="grid grid-cols-8 md:grid-cols-12 gap-3 mb-4">
                            @php
                                $colors = [
                                    '#3B82F6' => 'Blau',
                                    '#10B981' => 'Grün',
                                    '#EF4444' => 'Rot',
                                    '#F59E0B' => 'Gelb',
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
                                    class="color-option {{ old('color', $team->color) === $colorCode ? 'selected' : '' }}" 
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

                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex items-center justify-between text-white">
                            <div>
                                <label class="text-sm font-semibold text-gray-700 flex items-center">
                                    <i class="fas fa-toggle-on text-gray-400 mr-2"></i>
                                    Team-Status
                                </label>
                                <p class="text-sm text-gray-500 mt-1 text-white">
                                    Aktive Teams sind für alle Mitglieder sichtbar und zugänglich
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
                                <div class="flex items-center text-green-700 bg-green-50 p-4 rounded-lg">
                                    <i class="fas fa-check-circle text-xl mr-3"></i>
                                    <div>
                                        <p class="font-semibold text-white">Team ist Aktiv</p>
                                        <p class="text-sm text-white">Mitglieder können zugreifen und zusammenarbeiten</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center text-gray-700 bg-gray-50 p-4 rounded-lg">
                                    <i class="fas fa-pause-circle text-xl mr-3"></i>
                                    <div>
                                        <p class="font-semibold text-white">Team ist Inaktiv</p>
                                        <p class="text-sm text-white">Mitglieder können auf dieses Team nicht zugreifen</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 text-white">
                            <i class="fas fa-info-circle text-gray-400 mr-2"></i>Team-Informationen
                        </h4>
                        <div class="grid md:grid-cols-2 gap-4 text-sm text-white">
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                <span class="text-gray-600">Erstellt:</span>
                                <span class="ml-2 font-medium text-gray-900">{{ $team->created_at->format('d.m.Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-user text-gray-400 mr-2"></i>
                                <span class="text-gray-600">Erstellt von:</span>
                                <span class="ml-2 font-medium text-gray-900">{{ $team->creator->name }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-users text-gray-400 mr-2"></i>
                                <span class="text-gray-600">Mitglieder gesamt:</span>
                                <span class="ml-2 font-medium text-gray-900">{{ $team->members->count() }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock text-gray-400 mr-2"></i>
                                <span class="text-gray-600">Zuletzt aktualisiert:</span>
                                <span class="ml-2 font-medium text-gray-900">{{ $team->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <div class="flex items-center space-x-3 text-white">
                        <a href="{{ route('tenant.teams.show', ['tenantId' => $tenant->id, 'team' => $team->id]) }}" 
                           class="px-6 py-2 text-gray-700 hover:bg-gray-200 rounded-lg transition font-medium">
                            <i class="fas fa-times mr-2"></i>Abbrechen
                        </a>
                        
                        <button 
                            type="button"
                            onclick="resetForm()"
                            class="px-6 py-2 text-gray-700 hover:bg-gray-200 rounded-lg transition font-medium">
                            <i class="fas fa-undo mr-2"></i>Zurücksetzen
                        </button>
                    </div>
                    
                    <button 
                        type="submit"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition transform hover:scale-105 shadow-md">
                        <i class="fas fa-save mr-2 text-white"></i>Änderungen speichern
                    </button>
                </div>

            </form>
        </div>

        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex text-white">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3 mt-1"></i>
                <div class="text-white">
                    <h4 class="font-semibold text-yellow-900 mb-2">Wichtige Hinweise:</h4>
                    <ul class="text-sm text-yellow-800 space-y-1">
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>Änderungen wirken sich sofort auf alle Teammitglieder aus</li>
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>Die Deaktivierung schränkt den Zugriff der Mitglieder ein</li>
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>Die Teamfarbe hilft bei der visuellen Identifizierung</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-white">
                <div>
                    <h4 class="font-semibold text-red-900 mb-2 flex items-center text-white">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        Gefahrenzone
                    </h4>
                    <p class="text-sm text-red-800 text-white">
                        Das Löschen dieses Teams entfernt alle Mitglieder-Zugehörigkeiten. Diese Aktion kann nicht rückgängig gemacht werden.
                    </p>
                </div>
                <form method="POST" action="{{ route('tenant.teams.destroy', ['tenantId' => $tenant->id, 'team' => $team->id]) }}" 
                      onsubmit="return confirm('⚠️ Sind Sie absolut sicher?\n\nDies wird das Team dauerhaft löschen:\n• Team: {{ $team->name }}\n• Mitglieder: {{ $team->members->count() }}\n\nDIESE AKTION KANN NICHT RÜCKGÄNGIG GEMACHT WERDEN!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition whitespace-nowrap shadow-md">
                        <i class="fas fa-trash-alt mr-2 text-white"></i>Team löschen
                    </button>
                </form>
            </div>
        </div>

    </div>

    <script>
        // Farbauswahl
        function selectColor(color) {
            document.getElementById('colorInput').value = color;
            document.getElementById('colorCode').textContent = color;
            document.getElementById('customColor').value = color;
            
            // Markierung aktualisieren
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
            
            updatePreview();
        }

        // Hilfsfunktion für HEX Vergleich
        function rgbToHex(rgb) {
            if(!rgb.startsWith('rgb')) return rgb;
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

        // Vorschau aktualisieren
        function updatePreview() {
            const name = document.getElementById('name').value;
            const color = document.getElementById('colorInput').value;
            
            document.getElementById('teamPreviewName').textContent = name || 'Team-Name';
            
            const icon = document.getElementById('teamPreviewIcon');
            icon.style.backgroundColor = color;
            icon.textContent = name ? name.charAt(0).toUpperCase() : 'T';
        }

        // Status-Vorschau aktualisieren
        function updateStatusPreview(checkbox) {
            const preview = document.getElementById('statusPreview');
            
            if (checkbox.checked) {
                preview.innerHTML = `
                    <div class="flex items-center text-green-700 bg-green-50 p-4 rounded-lg">
                        <i class="fas fa-check-circle text-xl mr-3"></i>
                        <div>
                            <p class="font-semibold text-white">Team ist Aktiv</p>
                            <p class="text-sm text-white">Mitglieder können zugreifen und zusammenarbeiten</p>
                        </div>
                    </div>
                `;
            } else {
                preview.innerHTML = `
                    <div class="flex items-center text-gray-700 bg-gray-50 p-4 rounded-lg">
                        <i class="fas fa-pause-circle text-xl mr-3"></i>
                        <div>
                            <p class="font-semibold text-white">Team ist Inaktiv</p>
                            <p class="text-sm text-white">Mitglieder können auf dieses Team nicht zugreifen</p>
                        </div>
                    </div>
                `;
            }
        }

        // Zeichenzähler
        const descriptionInput = document.getElementById('description');
        const charCount = document.getElementById('charCount');
        
        descriptionInput.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });

        // Formular zurücksetzen
        function resetForm() {
            if (confirm('Alle Änderungen auf die ursprünglichen Werte zurücksetzen?')) {
                document.getElementById('editTeamForm').reset();
                selectColor('{{ $team->color }}');
                charCount.textContent = '{{ strlen($team->description) }}';
                updatePreview();
                updateStatusPreview(document.querySelector('input[name="is_active"]'));
            }
        }

        // Validierung
        document.getElementById('editTeamForm').addEventListener('submit', function(e) {
            const teamName = document.getElementById('name').value.trim();
            
            if (teamName.length < 3) {
                e.preventDefault();
                alert('Der Teamname muss mindestens 3 Zeichen lang sein.');
                document.getElementById('name').focus();
                return false;
            }
        });

        // Initialisierung
        document.addEventListener('DOMContentLoaded', function() {
            updatePreview();
        });
    </script>

</body>
</html>