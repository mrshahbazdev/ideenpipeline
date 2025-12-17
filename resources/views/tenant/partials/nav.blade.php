<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left Side -->
            <div class="flex items-center space-x-6">
                <!-- Logo/Brand -->
                <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" class="text-xl font-bold text-indigo-600 hover:text-indigo-700 transition">
                    <i class="fas fa-building mr-2"></i>{{ $tenant->subdomain }}
                </a>

                <!-- Role Badge -->
                @if(Auth::user()->isAdmin())
                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                        <i class="fas fa-crown mr-1"></i>ADMIN
                    </span>
                @elseif(Auth::user()->isDeveloper())
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">
                        <i class="fas fa-code mr-1"></i>DEVELOPER
                    </span>
                @elseif(Auth::user()->isWorkBee())
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                        <i class="fas fa-user-friends mr-1"></i>WORK-BEE
                    </span>
                @else
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                        <i class="fas fa-user mr-1"></i>STANDARD
                    </span>
                @endif

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-1">
                    <!-- Dashboard -->
                    <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" 
                       class="px-3 py-2 rounded-lg text-sm font-medium transition
                              {{ request()->routeIs('tenant.dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>

                    <!-- My Teams (All Users) -->
                    <a href="{{ route('tenant.my-teams', ['tenantId' => $tenant->id]) }}" 
                       class="px-3 py-2 rounded-lg text-sm font-medium transition
                              {{ request()->routeIs('tenant.my-teams') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-users mr-2"></i>My Teams
                    </a>

                    <!-- Teams Management (Admin Only) -->
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('tenant.teams.index', ['tenantId' => $tenant->id]) }}" 
                           class="px-3 py-2 rounded-lg text-sm font-medium transition
                                  {{ request()->routeIs('tenant.teams.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-users-cog mr-2"></i>Manage Teams
                        </a>
                    @endif

                    <!-- Ideas -->
                    <a href="#" 
                       class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                        <i class="fas fa-lightbulb mr-2"></i>Ideas
                    </a>

                    <!-- Pipeline -->
                    <a href="#" 
                       class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                        <i class="fas fa-stream mr-2"></i>Pipeline
                    </a>
                </div>
            </div>

            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                <!-- Current Team Indicator (Desktop) -->
                @php
                    $currentTeamId = session('current_team_id');
                    $currentTeam = $currentTeamId ? \App\Models\Team::find($currentTeamId) : Auth::user()->teams->first();
                @endphp
                
                @if($currentTeam)
                    <div class="hidden lg:flex items-center" x-data="{ teamOpen: false }">
                        <button @click="teamOpen = !teamOpen" class="flex items-center px-3 py-2 bg-gradient-to-r from-gray-100 to-gray-200 rounded-lg hover:from-gray-200 hover:to-gray-300 transition">
                            <div class="w-6 h-6 rounded flex items-center justify-center text-white text-xs font-bold mr-2" 
                                 style="background: {{ $currentTeam->color }}">
                                {{ strtoupper(substr($currentTeam->name, 0, 1)) }}
                            </div>
                            <div class="text-left mr-2">
                                <p class="text-xs text-gray-500">Active Team</p>
                                <p class="text-sm font-semibold text-gray-900">{{ Str::limit($currentTeam->name, 15) }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </button>

                        <!-- Team Switcher Dropdown -->
                        <div x-show="teamOpen" 
                             @click.away="teamOpen = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-xl border border-gray-200 py-2"
                             style="display: none; top: 4rem;">
                            
                            <div class="px-4 py-2 border-b border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 uppercase">Switch Team</p>
                            </div>

                            <div class="max-h-64 overflow-y-auto">
                                @foreach(Auth::user()->teams()->where('teams.tenant_id', $tenant->id)->get() as $team)
                                    <form method="POST" action="{{ route('tenant.teams.switch', ['tenantId' => $tenant->id, 'team' => $team->id]) }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center px-4 py-3 hover:bg-gray-50 transition
                                            {{ $currentTeam && $currentTeam->id === $team->id ? 'bg-indigo-50' : '' }}">
                                            <div class="w-8 h-8 rounded flex items-center justify-center text-white font-bold text-sm mr-3" 
                                                 style="background: {{ $team->color }}">
                                                {{ strtoupper(substr($team->name, 0, 1)) }}
                                            </div>
                                            <div class="flex-1 text-left">
                                                <p class="text-sm font-medium text-gray-900">{{ $team->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $team->member_count }} members</p>
                                            </div>
                                            @if($currentTeam && $currentTeam->id === $team->id)
                                                <i class="fas fa-check-circle text-green-500"></i>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            </div>

                            <div class="border-t border-gray-200 mt-2">
                                <a href="{{ route('tenant.my-teams', ['tenantId' => $tenant->id]) }}" 
                                   class="flex items-center px-4 py-2 text-sm text-indigo-600 hover:bg-indigo-50 transition">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    View All Teams
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Search (Hidden on mobile) -->
                <div class="hidden lg:block">
                    <div class="relative">
                        <input 
                            type="text" 
                            placeholder="Search ideas..."
                            class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
                        >
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>

                <!-- Notifications -->
                <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                <!-- User Menu Dropdown -->
                <div class="relative" x-data="{ userOpen: false }">
                    <button @click="userOpen = !userOpen" 
                            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition">
                        <!-- User Avatar -->
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-semibold text-sm
                            {{ Auth::user()->role === 'admin' ? 'bg-red-500' : '' }}
                            {{ Auth::user()->role === 'developer' ? 'bg-purple-500' : '' }}
                            {{ Auth::user()->role === 'work-bee' ? 'bg-green-500' : '' }}
                            {{ Auth::user()->role === 'standard' ? 'bg-blue-500' : '' }}
                        ">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        
                        <!-- User Info (Hidden on mobile) -->
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst(str_replace('-', ' ', Auth::user()->role)) }}</p>
                        </div>
                        
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>

                    <!-- User Dropdown Menu -->
                    <div x-show="userOpen" 
                         @click.away="userOpen = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 py-1"
                         style="display: none;">
                        
                        <!-- User Info -->
                        <div class="px-4 py-3 border-b border-gray-200">
                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>

                        <!-- Menu Items -->
                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                            <i class="fas fa-user-circle w-5 mr-3 text-gray-400"></i>
                            My Profile
                        </a>

                        <a href="{{ route('tenant.my-teams', ['tenantId' => $tenant->id]) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                            <i class="fas fa-users w-5 mr-3 text-gray-400"></i>
                            My Teams
                        </a>

                        @if(Auth::user()->isAdmin())
                            <div class="border-t border-gray-200 my-1"></div>
                            
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                <i class="fas fa-cog w-5 mr-3 text-gray-400"></i>
                                Settings
                            </a>

                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                <i class="fas fa-users-cog w-5 mr-3 text-gray-400"></i>
                                Manage Users
                            </a>

                            <a href="{{ route('tenant.teams.index', ['tenantId' => $tenant->id]) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                <i class="fas fa-shield-alt w-5 mr-3 text-gray-400"></i>
                                Manage Teams
                            </a>
                        @endif

                        <div class="border-t border-gray-200 my-1"></div>

                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                            <i class="fas fa-question-circle w-5 mr-3 text-gray-400"></i>
                            Help & Support
                        </a>

                        <div class="border-t border-gray-200 my-1"></div>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('tenant.logout', ['tenantId' => $tenant->id]) }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" 
         @click.away="mobileMenuOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="md:hidden border-t border-gray-200 bg-white"
         style="display: none;">
        
        <div class="px-4 py-3 space-y-1">
            <!-- Current Team (Mobile) -->
            @if($currentTeam)
                <div class="mb-4 p-3 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-200">
                    <p class="text-xs text-indigo-600 font-semibold mb-1">ACTIVE TEAM</p>
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded flex items-center justify-center text-white font-bold text-sm mr-2" 
                             style="background: {{ $currentTeam->color }}">
                            {{ strtoupper(substr($currentTeam->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $currentTeam->name }}</p>
                            <p class="text-xs text-gray-600">{{ $currentTeam->member_count }} members</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Dashboard -->
            <a href="{{ route('tenant.dashboard', ['tenantId' => $tenant->id]) }}" 
               class="block px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('tenant.dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </a>

            <!-- My Teams -->
            <a href="{{ route('tenant.my-teams', ['tenantId' => $tenant->id]) }}" 
               class="block px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('tenant.my-teams') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-users mr-2"></i>My Teams
            </a>

            <!-- Teams Management (Admin Only) -->
            @if(Auth::user()->isAdmin())
                <a href="{{ route('tenant.teams.index', ['tenantId' => $tenant->id]) }}" 
                   class="block px-3 py-2 rounded-lg text-sm font-medium
                          {{ request()->routeIs('tenant.teams.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-users-cog mr-2"></i>Manage Teams
                </a>
            @endif

            <!-- Ideas -->
            <a href="#" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100">
                <i class="fas fa-lightbulb mr-2"></i>Ideas
            </a>

            <!-- Pipeline -->
            <a href="#" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100">
                <i class="fas fa-stream mr-2"></i>Pipeline
            </a>
        </div>
    </div>
</nav>

<!-- Alpine.js for dropdowns -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
