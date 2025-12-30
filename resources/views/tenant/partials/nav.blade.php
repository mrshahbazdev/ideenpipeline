<nav class="bg-white shadow-lg sticky top-0 z-50 border-b border-gray-200" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-4">
                <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" 
                   class="flex items-center space-x-2 text-xl font-bold text-indigo-600 hover:text-indigo-700 transition">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                        <i class="fas fa-lightbulb text-white text-lg"></i>
                    </div>
                    <span class="hidden sm:block">{{ ucfirst($tenant->subdomain) }}</span>
                </a>

                <div class="hidden md:flex items-center space-x-1 ml-8">
                    <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('tenant.dashboard') ? 'bg-indigo-100 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>

                    <a href="{{ route('tenant.my-teams', ['tenantId' => $tenant->id]) }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('tenant.my-teams') ? 'bg-indigo-100 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <i class="fas fa-users mr-2"></i>Meine Teams
                    </a>

                    <div class="relative" x-data="{ ideasOpen: false }">
                        <button @click="ideasOpen = !ideasOpen"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center
                                       {{ request()->routeIs('tenant.ideas.*') ? 'bg-indigo-100 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i class="fas fa-lightbulb mr-2"></i>
                            Ideen
                            <i class="fas fa-chevron-down ml-2 text-xs transition-transform" :class="{ 'rotate-180': ideasOpen }"></i>
                        </button>
                        
                        <div x-show="ideasOpen" 
                             @click.away="ideasOpen = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-50"
                             style="display: none;">
                            
                            <a href="{{ route('tenant.ideas.index', ['tenantId' => $tenant->id]) }}" 
                               class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                <i class="fas fa-th-large w-5 mr-3 text-indigo-500"></i>
                                <span class="font-medium">Kartenansicht</span>
                            </a>
                            
                            <a href="{{ route('tenant.ideas.table', ['tenantId' => $tenant->id]) }}" 
                               class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                <i class="fas fa-table w-5 mr-3 text-indigo-500"></i>
                                <span class="font-medium">Tabellenansicht</span>
                            </a>
                            
                            <div class="border-t border-gray-100 my-2"></div>
                            
                            <a href="{{ route('tenant.ideas.create', ['tenantId' => $tenant->id]) }}" 
                               class="flex items-center px-4 py-3 text-sm text-indigo-600 hover:bg-indigo-50 transition-colors font-semibold">
                                <i class="fas fa-plus-circle w-5 mr-3"></i>
                                Neue Idee einreichen
                            </a>
                        </div>
                    </div>

                    @if(Auth::user()->isAdmin())
                        <div class="relative" x-data="{ adminOpen: false }">
                            <button @click="adminOpen = !adminOpen"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center
                                        {{ request()->routeIs('tenant.teams.*', 'tenant.admin.*') ? 'bg-red-100 text-red-700 shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <i class="fas fa-shield-alt mr-2"></i>
                                Admin
                                <i class="fas fa-chevron-down ml-2 text-xs transition-transform" :class="{ 'rotate-180': adminOpen }"></i>
                            </button>
                            
                            <div x-show="adminOpen" 
                                @click.away="adminOpen = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-50"
                                style="display: none;">
                                
                                <a href="{{ route('tenant.admin.users.index', ['tenantId' => $tenant->id]) }}" 
                                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                                    <i class="fas fa-users-cog w-5 mr-3 text-red-500"></i>
                                    <span class="font-medium">Benutzerverwaltung</span>
                                </a>
                                
                                <a href="{{ route('tenant.teams.index', ['tenantId' => $tenant->id]) }}" 
                                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                                    <i class="fas fa-users w-5 mr-3 text-red-500"></i>
                                    <span class="font-medium">Teamverwaltung</span>
                                </a>

                                <div class="border-t border-gray-100 my-2"></div>
                                
                                <a href="{{ route('tenant.admin.analytics', ['tenantId' => $tenant->id]) }}" 
                                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                                    <i class="fas fa-chart-line w-5 mr-3 text-red-500"></i>
                                    <span class="font-medium">Analysen</span>
                                </a>
                                
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            <div class="flex items-center space-x-3">
                @php
                    $currentTeamId = session('current_team_id');
                    $currentTeam = $currentTeamId ? \App\Models\Team::find($currentTeamId) : Auth::user()->teams->first();
                @endphp
                
                @if($currentTeam)
                    <div class="hidden lg:block" x-data="{ teamOpen: false }">
                        <button @click="teamOpen = !teamOpen" 
                                class="flex items-center space-x-3 px-4 py-2 bg-gradient-to-r from-indigo-50 to-purple-50 hover:from-indigo-100 hover:to-purple-100 rounded-lg transition-all duration-200 border border-indigo-200 shadow-sm">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm font-bold shadow-md" 
                                 style="background: {{ $currentTeam->color }}">
                                {{ strtoupper(substr($currentTeam->name, 0, 1)) }}
                            </div>
                            <div class="text-left text-white">
                                <p class="text-xs text-indigo-600 font-semibold">Aktives Team</p>
                                <p class="text-sm font-bold text-gray-900">{{ Str::limit($currentTeam->name, 12) }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-indigo-400 text-xs transition-transform" :class="{ 'rotate-180': teamOpen }"></i>
                        </button>

                        <div x-show="teamOpen" 
                             @click.away="teamOpen = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-50"
                             style="display: none; top: 3.5rem;">
                            
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Team wechseln</p>
                            </div>

                            <div class="max-h-64 overflow-y-auto py-2">
                                @foreach(Auth::user()->teams()->where('teams.tenant_id', $tenant->id)->get() as $team)
                                    <form method="POST" action="{{ route('tenant.teams.switch', ['tenantId' => $tenant->id, 'team' => $team->id]) }}">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full flex items-center px-4 py-3 hover:bg-indigo-50 transition-colors group
                                                       {{ $currentTeam && $currentTeam->id === $team->id ? 'bg-indigo-50 border-l-4 border-indigo-500' : '' }}">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-3 shadow-md group-hover:scale-110 transition-transform" 
                                                 style="background: {{ $team->color }}">
                                                {{ strtoupper(substr($team->name, 0, 1)) }}
                                            </div>
                                            <div class="flex-1 text-left">
                                                <p class="text-sm font-semibold text-gray-900">{{ $team->name }}</p>
                                                <p class="text-xs text-gray-500">
                                                    <i class="fas fa-users mr-1"></i>{{ $team->member_count }} Mitglieder
                                                </p>
                                            </div>
                                            @if($currentTeam && $currentTeam->id === $team->id)
                                                <i class="fas fa-check-circle text-green-500 text-lg"></i>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            </div>

                            <div class="border-t border-gray-100 mt-2">
                                <a href="{{ route('tenant.my-teams', ['tenantId' => $tenant->id]) }}" 
                                   class="flex items-center px-4 py-3 text-sm text-indigo-600 hover:bg-indigo-50 transition-colors font-semibold">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    Alle Teams anzeigen
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="hidden md:block">
                    @if(Auth::user()->isAdmin())
                        <span class="px-3 py-1.5 bg-gradient-to-r from-red-100 to-pink-100 text-red-800 text-xs font-bold rounded-full border border-red-200 shadow-sm">
                            <i class="fas fa-crown mr-1"></i>ADMIN
                        </span>
                    @elseif(Auth::user()->isDeveloper())
                        <span class="px-3 py-1.5 bg-gradient-to-r from-purple-100 to-indigo-100 text-purple-800 text-xs font-bold rounded-full border border-purple-200 shadow-sm">
                            <i class="fas fa-code mr-1"></i>ENTWICKLER
                        </span>
                    @elseif(Auth::user()->isWorkBee())
                        <span class="px-3 py-1.5 bg- gradient-to-r from-green-100 to-emerald-100 text-green-800 text-xs font-bold rounded-full border border-green-200 shadow-sm">
                            <i class="fas fa-user-friends mr-1"></i>WORK-BEE
                        </span>
                    @else
                        <span class="px-3 py-1.5 bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-800 text-xs font-bold rounded-full border border-blue-200 shadow-sm">
                            <i class="fas fa-user mr-1"></i>STANDARD
                        </span>
                    @endif
                </div>

                <div class="relative" x-data="{ userOpen: false }">
                    <button @click="userOpen = !userOpen" 
                            class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-all duration-200">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md
                            {{ Auth::user()->role === 'admin' ? 'bg-gradient-to-br from-red-500 to-pink-600' : '' }}
                            {{ Auth::user()->role === 'developer' ? 'bg-gradient-to-br from-purple-500 to-indigo-600' : '' }}
                            {{ Auth::user()->role === 'work-bee' ? 'bg-gradient-to-br from-green-500 to-emerald-600' : '' }}
                            {{ Auth::user()->role === 'standard' ? 'bg-gradient-to-br from-blue-500 to-cyan-600' : '' }}
                        ">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-semibold text-gray-900">{{ Str::limit(Auth::user()->name, 15) }}</p>
                            <p class="text-xs text-gray-500">
                                {{ Auth::user()->role === 'admin' ? 'Administrator' : (Auth::user()->role === 'developer' ? 'Entwickler' : ucfirst(str_replace('-', ' ', Auth::user()->role))) }}
                            </p>
                        </div>
                        
                        <i class="fas fa-chevron-down text-gray-400 text-xs hidden md:block transition-transform" :class="{ 'rotate-180': userOpen }"></i>
                    </button>

                    <div x-show="userOpen" 
                         @click.away="userOpen = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-50"
                         style="display: none;">
                        
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ Auth::user()->email }}</p>
                        </div>

                        <div class="py-2">
                            <a href="{{ route('tenant.my-teams', ['tenantId' => $tenant->id]) }}" 
                               class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                <i class="fas fa-users w-5 mr-3 text-indigo-500"></i>
                                <span class="font-medium">Meine Teams</span>
                            </a>

                            @if(Auth::user()->isAdmin())
                                <div class="border-t border-gray-100 my-2"></div>
                                
                                <a href="{{ route('tenant.teams.index', ['tenantId' => $tenant->id]) }}" 
                                   class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                                    <i class="fas fa-shield-alt w-5 mr-3 text-red-500"></i>
                                    <span class="font-medium">Admin-Bereich</span>
                                </a>
                            @endif
                        </div>

                        <div class="border-t border-gray-100 my-2"></div>

                        <form method="POST" action="{{ route('tenant.logout', ['tenantId' => $tenant->id]) }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors font-semibold">
                                <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                                Abmelden
                            </button>
                        </form>
                    </div>
                </div>

                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <div x-show="mobileMenuOpen" 
         @click.away="mobileMenuOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="md:hidden border-t border-gray-200 bg-white shadow-lg"
         style="display: none;">
        
        <div class="px-4 py-4 space-y-2">
            @if($currentTeam)
                <div class="mb-4 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-200 shadow-sm">
                    <p class="text-xs text-indigo-600 font-bold mb-2 uppercase tracking-wider">Aktives Team</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-3 shadow-md" 
                             style="background: {{ $currentTeam->color }}">
                            {{ strtoupper(substr($currentTeam->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $currentTeam->name }}</p>
                            <p class="text-xs text-gray-600">
                                <i class="fas fa-users mr-1"></i>{{ $currentTeam->member_count }} Mitglieder
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" 
               class="block px-4 py-3 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('tenant.dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-home mr-3"></i>Dashboard
            </a>

            <a href="{{ route('tenant.my-teams', ['tenantId' => $tenant->id]) }}" 
               class="block px-4 py-3 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('tenant.my-teams') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-users mr-3"></i>Meine Teams
            </a>

            <a href="{{ route('tenant.ideas.index', ['tenantId' => $tenant->id]) }}" 
               class="block px-4 py-3 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('tenant.ideas.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-lightbulb mr-3"></i>Ideen
            </a>

            @if(Auth::user()->isAdmin())
                <a href="{{ route('tenant.teams.index', ['tenantId' => $tenant->id]) }}" 
                   class="block px-4 py-3 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('tenant.teams.index') ? 'bg-red-100 text-red-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-shield-alt mr-3"></i>Admin-Bereich
                </a>
            @endif
        </div>
    </div>
</nav>