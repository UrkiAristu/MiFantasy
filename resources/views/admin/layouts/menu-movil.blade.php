<!-- Barra de Navegación Flotante Inferior Móvil (Admin) -->
<nav class="fixed bottom-4 inset-x-0 z-40 md:hidden flex justify-center px-4 pointer-events-none">
    <div class="pointer-events-auto bg-zinc-900/90 backdrop-blur-xl border border-zinc-800/90 rounded-full w-[95%] max-w-md mx-auto flex flex-row items-center justify-around px-2 py-3 shadow-2xl shadow-zinc-950/80">

        <!-- Botón Atrás -->
        <button
            type="button"
            onclick="history.back()"
            class="p-2.5 text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/80 rounded-full transition-all flex items-center justify-center cursor-pointer"
            aria-label="Volver atrás"
        >
            <i class="bi bi-arrow-left text-lg"></i>
        </button>

        <div class="h-4 w-px bg-zinc-800"></div>

        <!-- Usuarios -->
        <a
            href="{{ url('/admin/usuarios') }}"
            class="p-2.5 {{ request()->is('admin/usuarios*') ? 'text-lime-400 bg-lime-400/10' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/80' }} rounded-full transition-all flex items-center justify-center"
            aria-label="Usuarios"
            title="Usuarios"
        >
            <i class="bi bi-people-fill text-lg"></i>
        </a>

        <!-- Liguillas -->
        <a
            href="{{ url('/admin/liguillas') }}"
            class="p-2.5 {{ request()->is('admin/liguillas*') ? 'text-lime-400 bg-lime-400/10' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/80' }} rounded-full transition-all flex items-center justify-center"
            aria-label="Liguillas"
            title="Liguillas"
        >
            <i class="bi bi-award-fill text-lg"></i>
        </a>

        <!-- Torneos -->
        <a
            href="{{ url('/admin/torneos') }}"
            class="p-2.5 {{ request()->is('admin/torneos*') ? 'text-lime-400 bg-lime-400/10' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/80' }} rounded-full transition-all flex items-center justify-center"
            aria-label="Torneos"
            title="Torneos"
        >
            <i class="bi bi-trophy-fill text-lg"></i>
        </a>

        <!-- Equipos -->
        <a
            href="{{ url('/admin/equipos') }}"
            class="p-2.5 {{ request()->is('admin/equipos*') ? 'text-lime-400 bg-lime-400/10' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/80' }} rounded-full transition-all flex items-center justify-center"
            aria-label="Equipos"
            title="Equipos"
        >
            <i class="bi bi-shield-fill text-lg"></i>
        </a>

        <!-- Jugadores -->
        <a
            href="{{ url('/admin/jugadores') }}"
            class="p-2.5 {{ request()->is('admin/jugadores*') ? 'text-lime-400 bg-lime-400/10' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/80' }} rounded-full transition-all flex items-center justify-center"
            aria-label="Jugadores"
            title="Jugadores"
        >
            <i class="bi bi-person-lines-fill text-lg"></i>
        </a>

    </div>
</nav>
