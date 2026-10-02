<!-- Header Superior de Usuario -->
<header class="sticky top-0 z-40 w-full bg-zinc-950/80 backdrop-blur-xl border-b border-zinc-800/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Logo y Marca -->
            <div class="flex items-center gap-6">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}" alt="MiFantasy" class="h-8 w-8 rounded-xl object-contain ring-1 ring-zinc-800 group-hover:ring-lime-400/50 transition-all">
                    <span class="text-base font-bold tracking-tight text-zinc-100 group-hover:text-lime-400 transition-colors">
                        MiFantasy
                    </span>
                </a>

                <!-- Enlaces de Escritorio -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ url('/user/liguillas') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800/60 transition-colors {{ request()->is('user/liguillas*') ? 'bg-zinc-800/80 text-lime-400 font-semibold' : '' }}">
                        <i class="bi bi-trophy text-xs mr-1.5 {{ request()->is('user/liguillas*') ? 'text-lime-400' : 'text-zinc-400' }}"></i>
                        Mis Liguillas
                    </a>
                    <a href="{{ url('/user/torneos') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800/60 transition-colors {{ request()->is('user/torneos*') ? 'bg-zinc-800/80 text-lime-400 font-semibold' : '' }}">
                        <i class="bi bi-plus-circle text-xs mr-1.5 {{ request()->is('user/torneos*') ? 'text-lime-400' : 'text-zinc-400' }}"></i>
                        Torneos Activos
                    </a>
                    <a href="{{ url('/user/unirseLiguilla') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800/60 transition-colors {{ request()->is('user/unirseLiguilla*') ? 'bg-zinc-800/80 text-lime-400 font-semibold' : '' }}">
                        <i class="bi bi-bookmark-plus text-xs mr-1.5 {{ request()->is('user/unirseLiguilla*') ? 'text-lime-400' : 'text-zinc-400' }}"></i>
                        Unirse a Liguilla
                    </a>
                </nav>
            </div>

            <!-- Acciones de Usuario a la Derecha -->
            <div class="flex items-center gap-3">
                @auth
                    @if(Auth::user()->admin)
                        <a href="{{ url('/zonaAdmin') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-lime-400/10 text-lime-400 border border-lime-400/20 hover:bg-lime-400/20 transition-colors">
                            <i class="bi bi-shield-lock-fill text-xs"></i>
                            <span class="hidden sm:inline">Panel Admin</span>
                        </a>
                    @endif

                    <!-- Perfil -->
                    <a href="{{ url('/user/perfil') }}" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium rounded-lg text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800/60 border border-zinc-800/80 transition-all">
                        <div class="w-5 h-5 rounded-full bg-lime-400/20 text-lime-400 flex items-center justify-center font-bold text-[10px]">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="hidden sm:inline font-semibold">{{ Auth::user()->name }}</span>
                    </a>

                    <!-- Logout -->
                    <form action="{{ url('/logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 text-zinc-400 hover:text-rose-400 hover:bg-zinc-900 rounded-lg border border-transparent hover:border-zinc-800 transition-colors cursor-pointer" title="Cerrar sesión">
                            <i class="bi bi-box-arrow-right text-sm"></i>
                        </button>
                    </form>
                @endauth

                <!-- Botón hamburguesa móvil -->
                <button type="button" onclick="toggleMobileMenu()" class="md:hidden p-2 text-zinc-400 hover:text-zinc-100 hover:bg-zinc-900 rounded-lg border border-zinc-800" aria-label="Abrir menú">
                    <i class="bi bi-list text-lg"></i>
                </button>
            </div>

        </div>
    </div>
</header>

<!-- Drawer / Menú lateral móvil -->
<div id="mobileDrawer" class="fixed inset-0 z-50 transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden flex pointer-events-none">
    <!-- Backdrop oscuro -->
    <div id="drawerBackdrop" onclick="toggleMobileMenu()" class="fixed inset-0 bg-zinc-950/80 backdrop-blur-sm opacity-0 transition-opacity duration-300 pointer-events-auto"></div>

    <!-- Panel Lateral -->
    <div class="relative w-72 max-w-[80vw] bg-zinc-900 border-r border-zinc-800 p-6 flex flex-col justify-between z-10 pointer-events-auto shadow-2xl shadow-zinc-950">
        <div>
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}" alt="MiFantasy" class="h-7 w-7 rounded-lg">
                    <span class="text-sm font-bold text-zinc-100">MiFantasy</span>
                </div>
                <button onclick="toggleMobileMenu()" class="p-1.5 text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800 rounded-lg">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>

            @auth
            <div class="mb-6 p-3 rounded-xl bg-zinc-950/60 border border-zinc-800 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-lime-400/20 text-lime-400 flex items-center justify-center font-bold text-xs">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-semibold text-zinc-100 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-zinc-400 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            @endauth

            <nav class="space-y-1.5 text-xs font-medium">
                @auth
                    @if(Auth::user()->admin)
                        <a href="{{ url('/zonaAdmin') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-lime-400/10 text-lime-400 border border-lime-400/20 font-semibold mb-3">
                            <i class="bi bi-shield-lock-fill text-sm"></i>
                            <span>Zona Admin</span>
                        </a>
                    @endif
                    <a href="{{ url('/user/perfil') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800">
                        <i class="bi bi-person text-sm text-zinc-400"></i>
                        <span>Mi Perfil</span>
                    </a>
                    <a href="{{ url('/user/liguillas') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800">
                        <i class="bi bi-trophy text-sm text-zinc-400"></i>
                        <span>Mis Liguillas</span>
                    </a>
                    <a href="{{ url('/user/torneos') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800">
                        <i class="bi bi-plus-circle text-sm text-zinc-400"></i>
                        <span>Torneos Activos</span>
                    </a>
                    <a href="{{ url('/user/unirseLiguilla') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800">
                        <i class="bi bi-bookmark-plus text-sm text-zinc-400"></i>
                        <span>Unirse a Liguilla</span>
                    </a>
                @endauth
            </nav>
        </div>

        @auth
        <div class="pt-4 border-t border-zinc-800">
            <form action="{{ url('/logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-zinc-950 hover:bg-rose-500/10 border border-zinc-800 hover:border-rose-500/20 text-rose-400 text-xs font-semibold transition-colors cursor-pointer">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
        @endauth
    </div>
</div>

<script>
    function toggleMobileMenu() {
        const drawer = document.getElementById('mobileDrawer');
        const backdrop = document.getElementById('drawerBackdrop');
        if (!drawer || !backdrop) return;

        const isOpen = !drawer.classList.contains('-translate-x-full');
        if (isOpen) {
            drawer.classList.add('-translate-x-full');
            backdrop.classList.add('opacity-0');
            backdrop.classList.remove('opacity-100');
        } else {
            drawer.classList.remove('-translate-x-full');
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
        }
    }
</script>
