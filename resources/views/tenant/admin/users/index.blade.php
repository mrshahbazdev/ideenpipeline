<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzerverwaltung - {{ $tenant->subdomain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .user-row {
            transition: all 0.3s ease;
        }
        .user-row:hover {
            background-color: #F9FAFB;
            transform: translateX(4px);
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ mobileMenuOpen: false, deleteModal: false, selectedUser: null }">

    @include('tenant.partials.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-8">
            <div class="flex flex-col md:flex-row items-center justify-between mb-4 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-users-cog text-red-600 mr-3"></i>
                        Benutzerverwaltung
                    </h1>
                    <p class="text-gray-600 mt-2">Verwalten Sie alle Benutzer Ihrer Organisation</p>
                </div>
                <a href="{{ route('tenant.admin.users.create', ['tenantId' => $tenant->id]) }}" 
                   class="px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-lg font-semibold hover:from-red-700 hover:to-pink-700 transition shadow-lg transform hover:scale-105 text-center">
                    <i class="fas fa-user-plus mr-2 text-white"></i>Neuen Benutzer anlegen
                </a>
            </div>

            <div class="flex items-center space-x-2 text-sm text-gray-600">
                <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" class="hover:text-indigo-600">Dashboard</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900 font-medium">Benutzerverwaltung</span>
            </div>
        </div>

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
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 text-white">
                <p class="text-xs text-gray-600 mb-1 uppercase font-bold">Benutzer gesamt</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 text-white">
                <p class="text-xs text-gray-600 mb-1 uppercase font-bold">Aktiv</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['active_users'] }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500 text-white">
                <p class="text-xs text-gray-600 mb-1 uppercase font-bold">Admins</p>
                <p class="text-3xl font-bold text-red-600">{{ $stats['admins'] }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 text-white">
                <p class="text-xs text-gray-600 mb-1 uppercase font-bold">Entwickler</p>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['developers'] }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 text-white">
                <p class="text-xs text-gray-600 mb-1 uppercase font-bold">Work-Bees</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['work_bees'] }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 text-white">
                <p class="text-xs text-gray-600 mb-1 uppercase font-bold">Standard</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['standard'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="searchInput"
                            placeholder="Nach Name oder E-Mail suchen..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                            oninput="filterUsers()"
                        >
                        <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <select id="roleFilter" onchange="filterUsers()" 
                            class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 text-gray-700">
                        <option value="">Alle Rollen</option>
                        <option value="admin">Admin</option>
                        <option value="developer">Entwickler</option>
                        <option value="work-bee">Work-Bee</option>
                        <option value="standard">Standard</option>
                    </select>

                    <select id="statusFilter" onchange="filterUsers()" 
                            class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 text-gray-700">
                        <option value="">Alle Status</option>
                        <option value="active">Aktiv</option>
                        <option value="inactive">Inaktiv</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden text-white">
            <div class="overflow-x-auto">
                <table class="w-full text-white">
                    <thead class="bg-gray-50 border-b-2 border-gray-200 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Benutzer</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Rolle</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aktivität</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Beigetreten</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="usersTableBody">
                        @forelse($users as $user)
                            <tr class="user-row" 
                                data-name="{{ strtolower($user->name) }}" 
                                data-email="{{ strtolower($user->email) }}"
                                data-role="{{ $user->role }}"
                                data-status="{{ $user->is_active ? 'active' : 'inactive' }}">
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold shadow-lg {{ $user->role_color }}">
                                            {{ $user->initials }}
                                        </div>
                                        <div class="text-white">
                                            <p class="font-semibold text-gray-900 text-base">{{ $user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                            @if($user->id === Auth::id())
                                                <span class="inline-block mt-1 px-2 py-0.5 bg-indigo-100 text-indigo-800 text-[10px] font-bold uppercase rounded">Sie</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-bold border shadow-sm {{ $user->role_badge }}">
                                        @if($user->role === 'admin')
                                            <i class="fas fa-crown mr-1"></i>ADMIN
                                        @elseif($user->role === 'developer')
                                            <i class="fas fa-code mr-1"></i>ENTWICKLER
                                        @elseif($user->role === 'work-bee')
                                            <i class="fas fa-user-friends mr-1"></i>WORK-BEE
                                        @else
                                            <i class="fas fa-user mr-1"></i>STANDARD
                                        @endif
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="{{ route('tenant.admin.users.toggle-status', ['tenantId' => $tenant->id, 'user' => $user->id]) }}" class="inline text-white">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold shadow-sm transition-all
                                                {{ $user->is_active ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-gray-100 text-gray-800 border border-gray-300' }}">
                                            <i class="fas fa-circle text-[8px] mr-1.5 {{ $user->is_active ? 'text-green-500' : 'text-gray-400' }}"></i>
                                            {{ $user->is_active ? 'Aktiv' : 'Inaktiv' }}
                                        </button>
                                    </form>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center space-x-4 text-[10px]">
                                        <div class="text-center" title="Teams">
                                            <i class="fas fa-users text-blue-500"></i>
                                            <span class="ml-1 font-bold text-gray-900">{{ $user->teams_count }}</span>
                                        </div>
                                        <div class="text-center" title="Ideen">
                                            <i class="fas fa-lightbulb text-yellow-500"></i>
                                            <span class="ml-1 font-bold text-gray-900">{{ $user->ideas_count }}</span>
                                        </div>
                                        <div class="text-center" title="Stimmen">
                                            <i class="fas fa-arrow-up text-green-500"></i>
                                            <span class="ml-1 font-bold text-gray-900">{{ $user->votes_count }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <p class="text-sm text-gray-900 font-medium">{{ $user->created_at->format('d.m.Y') }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center space-x-2 text-white">
                                        <a href="{{ route('tenant.admin.users.edit', ['tenantId' => $tenant->id, 'user' => $user->id]) }}" 
                                           class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg transition" 
                                           title="Bearbeiten">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        @if($user->id !== Auth::id())
                                            <button @click="selectedUser = {{ $user->id }}; deleteModal = true" 
                                                    class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition" 
                                                    title="Löschen">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <span class="p-2 text-gray-300 cursor-not-allowed" title="Man kann sich nicht selbst löschen">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-white">
                                    <i class="fas fa-users text-gray-300 text-5xl mb-4 text-white"></i>
                                    <p class="text-gray-600 mb-2">Keine Benutzer gefunden</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div x-show="deleteModal" 
         x-transition.opacity
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="deleteModal = false"></div>

            <div class="inline-block bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full sm:p-6 relative z-10 text-white">
                <div class="sm:flex sm:items-start text-white">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left text-white">
                        <h3 class="text-lg font-bold text-gray-900 text-white">Benutzer löschen</h3>
                        <div class="mt-2 text-white">
                            <p class="text-sm text-gray-500 text-white">
                                Sind Sie sicher, dass Sie diesen Benutzer löschen möchten? Diese Aktion kann nicht rückgängig gemacht werden. Alle Daten dieses Benutzers werden permanent entfernt.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse text-white">
                    <form :action="'/tenant/{{ $tenant->id }}/admin/users/' + selectedUser" method="POST" class="inline text-white">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full inline-flex justify-center rounded-lg px-4 py-2 bg-red-600 text-base font-semibold text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm shadow-md">
                            <i class="fas fa-trash mr-2 text-white"></i>Benutzer löschen
                        </button>
                    </form>
                    <button type="button" 
                            @click="deleteModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                        Abbrechen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterUsers() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const roleFilter = document.getElementById('roleFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            
            const rows = document.querySelectorAll('.user-row');
            
            rows.forEach(row => {
                const name = row.dataset.name;
                const email = row.dataset.email;
                const role = row.dataset.role;
                const status = row.dataset.status;
                
                const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                const matchesRole = !roleFilter || role === roleFilter;
                const matchesStatus = !statusFilter || status === statusFilter;
                
                if (matchesSearch && matchesRole && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>

</body>
</html>