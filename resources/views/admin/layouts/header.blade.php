<!-- Header Superior Admin (Tema Claro Minimalista) -->
<nav class="sticky top-0 z-40 w-full bg-white border-b border-zinc-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 flex-nowrap">

            <!-- Logo y Marca Admin -->
            <div class="flex items-center gap-6 flex-nowrap shrink-0">
                <a href="{{ url('/zonaAdmin') }}" class="flex items-center gap-2.5 group flex-nowrap">
                    <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}" alt="Logo Admin" class="w-10 h-10 max-w-[40px] max-h-[40px] object-contain rounded-full mr-2 shrink-0">
                    <span class="text-base font-bold tracking-tight text-zinc-900 group-hover:text-lime-600 transition-colors whitespace-nowrap">
                        Admin @auth {{ Auth::user()->name }} @else Panel @endauth
                    </span>
                </a>

                <!-- Navegación de Escritorio Admin -->
                <nav class="hidden md:flex items-center gap-1 flex-nowrap">
                    <a href="{{ url('/zonaAdmin') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 transition-colors whitespace-nowrap {{ request()->is('zonaAdmin') ? 'bg-zinc-100 text-zinc-900 font-semibold' : '' }}">
                        <i class="bi bi-speedometer2 text-xs mr-1.5 {{ request()->is('zonaAdmin') ? 'text-lime-600' : 'text-zinc-400' }}"></i>
                        Panel
                    </a>
                    <a href="{{ url('/admin/usuarios') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 transition-colors whitespace-nowrap {{ request()->is('admin/usuarios*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : '' }}">
                        <i class="bi bi-people-fill text-xs mr-1.5 {{ request()->is('admin/usuarios*') ? 'text-lime-600' : 'text-zinc-400' }}"></i>
                        Usuarios
                    </a>
                    <a href="{{ url('/admin/liguillas') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 transition-colors whitespace-nowrap {{ request()->is('admin/liguillas*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : '' }}">
                        <i class="bi bi-award-fill text-xs mr-1.5 {{ request()->is('admin/liguillas*') ? 'text-lime-600' : 'text-zinc-400' }}"></i>
                        Liguillas
                    </a>
                    <a href="{{ url('/admin/torneos') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 transition-colors whitespace-nowrap {{ request()->is('admin/torneos*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : '' }}">
                        <i class="bi bi-trophy-fill text-xs mr-1.5 {{ request()->is('admin/torneos*') ? 'text-lime-600' : 'text-zinc-400' }}"></i>
                        Torneos
                    </a>
                    <a href="{{ url('/admin/equipos') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 transition-colors whitespace-nowrap {{ request()->is('admin/equipos*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : '' }}">
                        <i class="bi bi-shield-fill text-xs mr-1.5 {{ request()->is('admin/equipos*') ? 'text-lime-600' : 'text-zinc-400' }}"></i>
                        Equipos
                    </a>
                    <a href="{{ url('/admin/jugadores') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 transition-colors whitespace-nowrap {{ request()->is('admin/jugadores*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : '' }}">
                        <i class="bi bi-person-lines-fill text-xs mr-1.5 {{ request()->is('admin/jugadores*') ? 'text-lime-600' : 'text-zinc-400' }}"></i>
                        Jugadores
                    </a>
                </nav>
            </div>

            <!-- Acciones Derecha Admin -->
            <div class="flex items-center gap-3 flex-nowrap shrink-0">
                <a href="{{ url('/') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg bg-lime-400 hover:bg-lime-300 text-zinc-950 transition-colors whitespace-nowrap shadow-sm">
                    <i class="bi bi-house-fill text-xs"></i>
                    <span>Volver a la App</span>
                </a>

                <!-- Botón hamburguesa móvil admin -->
                <button type="button" onclick="toggleAdminMenu()" class="md:hidden p-2 text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 rounded-lg border border-zinc-200 shrink-0 cursor-pointer" aria-label="Abrir menú">
                    <i class="bi bi-list text-lg"></i>
                </button>
            </div>

        </div>
    </div>
</nav>

<!-- Drawer / Menú lateral móvil Admin -->
<div id="adminMobileDrawer" class="fixed inset-0 z-50 transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden flex pointer-events-none">
    <div id="adminDrawerBackdrop" onclick="toggleAdminMenu()" class="fixed inset-0 bg-zinc-950/40 backdrop-blur-sm opacity-0 transition-opacity duration-300 pointer-events-auto"></div>

    <div class="absolute inset-y-0 left-0 w-72 max-w-[85vw] bg-white border-r border-zinc-200 p-6 flex flex-col justify-between z-10 pointer-events-auto shadow-2xl">
        <div>
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-zinc-200">
                <div class="flex items-center gap-2.5 flex-nowrap">
                    <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}" alt="MiFantasy" class="h-7 w-7 rounded-full shrink-0">
                    <span class="text-sm font-bold text-zinc-900 truncate">Panel Admin</span>
                </div>
                <button onclick="toggleAdminMenu()" class="p-1.5 text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 rounded-lg cursor-pointer">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>

            <nav class="space-y-1.5 text-xs font-medium">
                <a href="{{ url('/zonaAdmin') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-zinc-700 hover:bg-zinc-100 transition-colors {{ request()->is('zonaAdmin') ? 'bg-zinc-100 text-zinc-900 font-semibold' : '' }}">
                    <i class="bi bi-speedometer2 text-base text-zinc-400"></i>
                    <span>Panel de Inicio</span>
                </a>
                <a href="{{ url('/admin/usuarios') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-zinc-700 hover:bg-zinc-100 transition-colors {{ request()->is('admin/usuarios*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : '' }}">
                    <i class="bi bi-people-fill text-base text-zinc-400"></i>
                    <span>Usuarios</span>
                </a>
                <a href="{{ url('/admin/liguillas') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-zinc-700 hover:bg-zinc-100 transition-colors {{ request()->is('admin/liguillas*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : '' }}">
                    <i class="bi bi-award-fill text-base text-zinc-400"></i>
                    <span>Liguillas</span>
                </a>
                <a href="{{ url('/admin/torneos') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-zinc-700 hover:bg-zinc-100 transition-colors {{ request()->is('admin/torneos*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : '' }}">
                    <i class="bi bi-trophy-fill text-base text-zinc-400"></i>
                    <span>Torneos</span>
                </a>
                <a href="{{ url('/admin/equipos') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-zinc-700 hover:bg-zinc-100 transition-colors {{ request()->is('admin/equipos*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : '' }}">
                    <i class="bi bi-shield-fill text-base text-zinc-400"></i>
                    <span>Equipos</span>
                </a>
                <a href="{{ url('/admin/jugadores') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-zinc-700 hover:bg-zinc-100 transition-colors {{ request()->is('admin/jugadores*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : '' }}">
                    <i class="bi bi-person-lines-fill text-base text-zinc-400"></i>
                    <span>Jugadores</span>
                </a>
            </nav>
        </div>

        <div class="pt-4 border-t border-zinc-200 space-y-2">
            <a href="{{ url('/') }}" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold text-xs transition-colors shadow-sm">
                <i class="bi bi-house-fill"></i>
                <span>Volver a la App</span>
            </a>
            @auth
            <form action="{{ url('/logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 font-semibold text-xs transition-colors cursor-pointer">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Cerrar sesión</span>
                </button>
            </form>
            @endauth
        </div>
    </div>
</div>

<script>
    function toggleAdminMenu() {
        const drawer = document.getElementById('adminMobileDrawer');
        const backdrop = document.getElementById('adminDrawerBackdrop');
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
