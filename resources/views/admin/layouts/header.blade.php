<nav class="navbar navbar-expand-lg navbar-dark bg-zinc-950 border-b border-zinc-800 shadow-sm sticky-top">
    <div class="container-fluid flex items-center justify-between">
        <a class="navbar-brand d-flex align-items-center gap-4 text-decoration-none" href="{{ url('/zonaAdmin') }}">
            <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}" alt="Logo" class="w-10 h-10 object-contain">
            <span class="text-zinc-100 font-bold">
                Admin
                @auth
                {{ Auth::user()->name}}
                @else
                Panel
                @endauth
            </span>
        </a>

        <!-- Navegación de Escritorio (Desktop) -->
        <div class="hidden md:flex items-center gap-1.5">
            <a href="{{ url('/zonaAdmin') }}" class="px-3 py-2 rounded-lg text-xs font-semibold text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800 transition-colors {{ request()->is('zonaAdmin') ? 'bg-zinc-800 text-lime-400' : '' }}">Panel de Inicio</a>
            <a href="{{ url('/admin/usuarios') }}" class="px-3 py-2 rounded-lg text-xs font-semibold text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800 transition-colors {{ request()->is('admin/usuarios*') ? 'bg-zinc-800 text-lime-400' : '' }}">Usuarios</a>
            <a href="{{ url('/admin/liguillas') }}" class="px-3 py-2 rounded-lg text-xs font-semibold text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800 transition-colors {{ request()->is('admin/liguillas*') ? 'bg-zinc-800 text-lime-400' : '' }}">Liguillas</a>
            <a href="{{ url('/admin/torneos') }}" class="px-3 py-2 rounded-lg text-xs font-semibold text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800 transition-colors {{ request()->is('admin/torneos*') ? 'bg-zinc-800 text-lime-400' : '' }}">Torneos</a>
            <a href="{{ url('/admin/equipos') }}" class="px-3 py-2 rounded-lg text-xs font-semibold text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800 transition-colors {{ request()->is('admin/equipos*') ? 'bg-zinc-800 text-lime-400' : '' }}">Equipos</a>
            <a href="{{ url('/admin/jugadores') }}" class="px-3 py-2 rounded-lg text-xs font-semibold text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800 transition-colors {{ request()->is('admin/jugadores*') ? 'bg-zinc-800 text-lime-400' : '' }}">Jugadores</a>
        </div>

        <div class="flex items-center gap-3">
            <!-- Botón Volver a la App (PC) -->
            <a href="{{ url('/') }}" class="hidden md:inline-flex items-center gap-1.5 bg-lime-400 hover:bg-lime-500 text-zinc-950 px-3.5 py-2 rounded-lg text-xs font-bold transition-colors">
                <i class="bi bi-house"></i> Volver a la App
            </a>

            <!-- Botón Hamburguesa Móvil -->
            <button class="md:hidden p-2 text-zinc-400 hover:text-zinc-100 focus:outline-none border border-zinc-800 rounded-lg cursor-pointer" type="button" id="adminMenuBtn" aria-label="Abrir menú">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
    </div>
</nav>

<!-- Backdrop Oscuro -->
<div id="adminBackdrop" class="fixed inset-0 bg-zinc-950/80 backdrop-blur-sm z-40 hidden"></div>

<!-- Off-Canvas Drawer (Menú lateral izquierdo) -->
<div id="adminDrawer" class="fixed inset-y-0 left-0 w-64 bg-zinc-950 z-50 transform -translate-x-full transition-transform duration-300 shadow-2xl flex flex-col p-6">
    <div class="flex items-center justify-between pb-4 border-b border-zinc-800/80 mb-6">
        <a class="flex items-center gap-3 text-decoration-none" href="{{ url('/zonaAdmin') }}">
            <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}" alt="Logo" class="w-8 h-8 object-contain">
            <span class="text-zinc-100 font-bold text-sm">
                Admin @auth {{ Auth::user()->name }} @else Panel @endauth
            </span>
        </a>
        <button type="button" id="adminDrawerCloseBtn" class="p-1.5 text-zinc-400 hover:text-white rounded-lg hover:bg-zinc-900 transition-colors cursor-pointer" aria-label="Cerrar menú">
            <i class="bi bi-x-lg text-base"></i>
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto">
        <a class="text-zinc-200 hover:text-white hover:bg-zinc-900 px-3 py-2.5 rounded-xl transition-colors flex items-center gap-3 text-xs font-semibold {{ request()->is('zonaAdmin') ? 'bg-zinc-900 text-lime-400' : '' }}" href="{{ url('/zonaAdmin') }}">
            <i class="bi bi-speedometer2 text-base text-zinc-400"></i>
            <span>Panel de Inicio</span>
        </a>
        <a class="text-zinc-200 hover:text-white hover:bg-zinc-900 px-3 py-2.5 rounded-xl transition-colors flex items-center gap-3 text-xs font-semibold {{ request()->is('admin/usuarios*') ? 'bg-zinc-900 text-lime-400' : '' }}" href="{{ url('/admin/usuarios') }}">
            <i class="bi bi-people text-base text-zinc-400"></i>
            <span>Usuarios</span>
        </a>
        <a class="text-zinc-200 hover:text-white hover:bg-zinc-900 px-3 py-2.5 rounded-xl transition-colors flex items-center gap-3 text-xs font-semibold {{ request()->is('admin/liguillas*') ? 'bg-zinc-900 text-lime-400' : '' }}" href="{{ url('/admin/liguillas') }}">
            <i class="bi bi-award text-base text-zinc-400"></i>
            <span>Liguillas</span>
        </a>
        <a class="text-zinc-200 hover:text-white hover:bg-zinc-900 px-3 py-2.5 rounded-xl transition-colors flex items-center gap-3 text-xs font-semibold {{ request()->is('admin/torneos*') ? 'bg-zinc-900 text-lime-400' : '' }}" href="{{ url('/admin/torneos') }}">
            <i class="bi bi-trophy text-base text-zinc-400"></i>
            <span>Torneos</span>
        </a>
        <a class="text-zinc-200 hover:text-white hover:bg-zinc-900 px-3 py-2.5 rounded-xl transition-colors flex items-center gap-3 text-xs font-semibold {{ request()->is('admin/equipos*') ? 'bg-zinc-900 text-lime-400' : '' }}" href="{{ url('/admin/equipos') }}">
            <i class="bi bi-shield text-base text-zinc-400"></i>
            <span>Equipos</span>
        </a>
        <a class="text-zinc-200 hover:text-white hover:bg-zinc-900 px-3 py-2.5 rounded-xl transition-colors flex items-center gap-3 text-xs font-semibold {{ request()->is('admin/jugadores*') ? 'bg-zinc-900 text-lime-400' : '' }}" href="{{ url('/admin/jugadores') }}">
            <i class="bi bi-person-badge text-base text-zinc-400"></i>
            <span>Jugadores</span>
        </a>
    </nav>

    <div class="pt-4 border-t border-zinc-800/80 space-y-2 mt-auto">
        <a class="bg-lime-400 hover:bg-lime-500 text-zinc-950 font-bold px-3 py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2 text-xs" href="{{ url('/') }}">
            <i class="bi bi-house"></i>
            <span>Volver a la App</span>
        </a>
        <form action="{{ url('/logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" class="w-full text-rose-400 hover:text-rose-300 px-3 py-2.5 rounded-xl hover:bg-rose-500/10 transition-colors flex items-center justify-center gap-2 text-xs font-semibold cursor-pointer">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('adminMenuBtn');
        const drawer = document.getElementById('adminDrawer');
        const backdrop = document.getElementById('adminBackdrop');
        const closeBtn = document.getElementById('adminDrawerCloseBtn');

        function openDrawer() {
            if (drawer && backdrop) {
                drawer.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            }
        }

        function closeDrawer() {
            if (drawer && backdrop) {
                drawer.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        if (btn) {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                openDrawer();
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                closeDrawer();
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', () => {
                closeDrawer();
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeDrawer();
            }
        });
    });
</script>
