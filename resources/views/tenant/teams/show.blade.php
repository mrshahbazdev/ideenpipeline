<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $team->name }} - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .team-color-bg {
            background: linear-gradient(135deg, {{ $team->color }}20 0%, {{ $team->color }}40 100%);
        }
        
        .member-card {
            transition: all 0.3s ease;
        }
        
        .member-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-50">

    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center text-white">
                    <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" class="text-xl font-bold text-indigo-600">
                        <i class="fas fa-building mr-2"></i>{{ $tenant->subdomain }}
                    </a>
                    <span class="ml-3 px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                        <i class="fas fa-crown mr-1"></i>ADMIN
                    </span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('tenant.teams.index', ['tenantId' => $tenant->id]) }}" class="text-gray-600 hover:text-gray-900 font-medium transition">
                        <i class="fas fa-arrow-left mr-2"></i>Zurück zur Teamübersicht
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center text-white">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="team-color-bg rounded-xl shadow-lg p-8 mb-8">
            <div class="flex flex-col md:flex-row items-start justify-between gap-6">
                <div class="flex items-start space-x-6 text-white">
                    <div class="w-20 h-20 rounded-xl flex items-center justify-center text-white font-bold text-3xl shadow-lg" style="background: {{ $team->color }}">
                        {{ strtoupper(substr($team->name, 0, 1)) }}
                    </div>
                    
                    <div>
                        <div class="flex items-center space-x-3 mb-2">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $team->name }}</h1>
                            @if($team->is_active)
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                                    <i class="fas fa-check-circle mr-1"></i> Aktiv
                                </span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-semibold rounded-full">
                                    <i class="fas fa-pause-circle mr-1"></i> Inaktiv
                                </span>
                            @endif
                        </div>
                        
                        <p class="text-gray-700 text-lg mb-4">
                            {{ $team->description ?: 'Keine Beschreibung hinterlegt' }}
                        </p>
                        
                        <div class="flex flex-wrap items-center gap-6 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-users text-gray-400 mr-2"></i>
                                <span class="font-semibold">{{ $team->members->count() }}</span>
                                <span class="ml-1">Mitglieder</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                <span>Erstellt am {{ $team->created_at->format('d.m.Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-user text-gray-400 mr-2"></i>
                                <span>Von {{ $team->creator->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3 text-white">
                    <a href="{{ route('tenant.teams.edit', ['tenantId' => $tenant->id, 'team' => $team->id]) }}" 
                       class="px-4 py-2 bg-white text-gray-700 rounded-lg shadow hover:bg-gray-50 transition flex items-center border border-gray-200">
                        <i class="fas fa-edit mr-2"></i>Bearbeiten
                    </a>
                    <form method="POST" action="{{ route('tenant.teams.destroy', ['tenantId' => $tenant->id, 'team' => $team->id]) }}" 
                          onsubmit="return confirm('Sind Sie sicher, dass Sie dieses Team löschen möchten? Diese Aktion kann nicht rückgängig gemacht werden.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 transition flex items-center">
                            <i class="fas fa-trash mr-2"></i>Team löschen
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-4 gap-6 mb-8 text-white">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Mitglieder gesamt</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $team->members->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1 text-white">Entwickler</p>
                        <p class="text-3xl font-bold text-purple-600">
                            {{ $team->members->where('role', 'developer')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-code text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1 text-white">Work-Bees</p>
                        <p class="text-3xl font-bold text-green-600">
                            {{ $team->members->where('role', 'work-bee')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-friends text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1 text-white">Standard</p>
                        <p class="text-3xl font-bold text-indigo-600">
                            {{ $team->members->where('role', 'standard')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-indigo-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-between">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-users mr-3 text-white"></i>
                    Teammitglieder ({{ $team->members->count() }})
                </h3>
                <button 
                    onclick="document.getElementById('addMemberModal').classList.remove('hidden')"
                    class="px-4 py-2 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-gray-100 transition shadow-sm"
                >
                    <i class="fas fa-plus mr-2"></i>Mitglied hinzufügen
                </button>
            </div>

            @if($team->members->count() > 0)
                <div class="p-6">
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($team->members as $member)
                            <div class="member-card bg-white border border-gray-200 rounded-lg p-6 hover:border-indigo-300">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-14 h-14 rounded-full flex items-center justify-center text-white font-bold text-xl
                                        {{ $member->role === 'admin' ? 'bg-red-500' : '' }}
                                        {{ $member->role === 'developer' ? 'bg-purple-500' : '' }}
                                        {{ $member->role === 'work-bee' ? 'bg-green-500' : '' }}
                                        {{ $member->role === 'standard' ? 'bg-blue-500' : '' }}
                                    ">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>

                                    <form method="POST" action="{{ route('tenant.teams.remove-member', ['tenantId' => $tenant->id, 'team' => $team->id, 'user' => $member->id]) }}"
                                          onsubmit="return confirm('Möchten Sie {{ $member->name }} wirklich aus diesem Team entfernen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition" title="Mitglied entfernen">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-900 text-lg mb-1">{{ $member->name }}</h4>
                                    <p class="text-sm text-gray-600 mb-3">{{ $member->email }}</p>
                                    
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $member->getRoleBadgeClass() }}">
                                        <i class="fas {{ $member->getRoleIcon() }} mr-1"></i>
                                        {{ $member->role === 'developer' ? 'Entwickler' : ($member->role === 'admin' ? 'Administrator' : ucfirst(str_replace('-', ' ', $member->role))) }}
                                    </span>

                                    <p class="text-xs text-gray-500 mt-3">
                                        <i class="fas fa-calendar-check mr-1 text-white"></i>
                                        @if($member->pivot->joined_at instanceof \Carbon\Carbon)
                                            Beigetreten am {{ $member->pivot->joined_at->format('d.m.Y') }}
                                            <span class="text-gray-400">(vor {{ $member->pivot->joined_at->diffForHumans(null, true) }})</span>
                                        @elseif(is_string($member->pivot->joined_at))
                                            Beigetreten am {{ \Carbon\Carbon::parse($member->pivot->joined_at)->format('d.m.Y') }}
                                            <span class="text-gray-400">(vor {{ \Carbon\Carbon::parse($member->pivot->joined_at)->diffForHumans(null, true) }})</span>
                                        @else
                                            Vor kurzem beigetreten
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2 text-white">Noch keine Mitglieder</h3>
                    <p class="text-gray-600 mb-6">Beginnen Sie damit, Mitglieder zu Ihrem Team hinzuzufügen.</p>
                    <button 
                        onclick="document.getElementById('addMemberModal').classList.remove('hidden')"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition"
                    >
                        <i class="fas fa-plus mr-2"></i>Erstes Mitglied hinzufügen
                    </button>
                </div>
            @endif
        </div>

        <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-history text-indigo-600 mr-3"></i>
                Letzte Aktivitäten
            </h3>
            
            <div class="space-y-4">
                <div class="flex items-start space-x-4 pb-4 border-b border-gray-200 last:border-0">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">
                            <span class="font-semibold">{{ $team->creator->name }}</span> hat dieses Team erstellt
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $team->created_at->format('d.m.Y \u\m H:i') }} Uhr
                        </p>
                    </div>
                </div>

                @foreach($team->members->sortByDesc('pivot.joined_at')->take(5) as $member)
                    <div class="flex items-start space-x-4 pb-4 border-b border-gray-200 last:border-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 text-white">
                            <i class="fas fa-user-plus text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">
                                <span class="font-semibold">{{ $member->name }}</span> ist dem Team beigetreten
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                @if($member->pivot->joined_at instanceof \Carbon\Carbon)
                                    {{ $member->pivot->joined_at->format('d.m.Y \u\m H:i') }} Uhr
                                @elseif(is_string($member->pivot->joined_at))
                                    {{ \Carbon\Carbon::parse($member->pivot->joined_at)->format('d.m.Y \u\m H:i') }} Uhr
                                @else
                                    Kürzlich
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <div id="addMemberModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Mitglied hinzufügen</h3>
                <button onclick="document.getElementById('addMemberModal').classList.add('hidden')" 
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('tenant.teams.add-member', ['tenantId' => $tenant->id, 'team' => $team->id]) }}" class="p-6">
                @csrf

                @php
                    $availableUsers = \App\Models\User::where('tenant_id', $tenant->id)
                        ->whereNotIn('id', $team->members->pluck('id'))
                        ->get();
                @endphp

                @if($availableUsers->count() > 0)
                    <div class="space-y-3">
                        @foreach($availableUsers as $user)
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                <input type="radio" name="user_id" value="{{ $user->id }}" required class="mr-3 text-white">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold
                                    {{ $user->role === 'developer' ? 'bg-purple-500' : '' }}
                                    {{ $user->role === 'work-bee' ? 'bg-green-500' : '' }}
                                    {{ $user->role === 'standard' ? 'bg-blue-500' : '' }}
                                ">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="ml-3 flex-1 text-white">
                                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $user->getRoleBadgeClass() }}">
                                    {{ $user->role === 'developer' ? 'Entwickler' : ucfirst(str_replace('-', ' ', $user->role)) }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end space-x-3 text-white">
                        <button type="button" 
                                onclick="document.getElementById('addMemberModal').classList.add('hidden')"
                                class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                            Abbrechen
                        </button>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition">
                            <i class="fas fa-plus mr-2"></i>Mitglied hinzufügen
                        </button>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-users text-gray-300 text-5xl mb-4 text-white"></i>
                        <p class="text-gray-600">Alle verfügbaren Benutzer sind bereits in diesem Team.</p>
                    </div>
                @endif
            </form>
        </div>
    </div>

</body>
</html>