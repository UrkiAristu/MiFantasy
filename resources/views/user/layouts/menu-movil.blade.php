<!-- Barra de Navegación Flotante Móvil -->
<nav class="fixed bottom-4 inset-x-0 z-40 lg:hidden flex justify-center px-4 pointer-events-none">
    <div class="pointer-events-auto bg-zinc-900/90 backdrop-blur-xl border border-zinc-800/90 rounded-full px-2 py-1.5 shadow-2xl shadow-zinc-950/80 flex items-center gap-1 sm:gap-2">

        <!-- Botón Volver -->
        <button
            type="button"
            onclick="history.back()"
            class="p-3 text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/80 rounded-full transition-all flex items-center justify-center cursor-pointer"
            aria-label="Volver atrás"
        >
            <i class="bi bi-arrow-left text-lg"></i>
        </button>

        <div class="h-5 w-px bg-zinc-800"></div>

        <!-- Mis Liguillas -->
        <a
            href="{{ url('/user/liguillas') }}"
            class="p-3 {{ request()->is('user/liguillas*') ? 'text-lime-400 bg-lime-400/10' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/80' }} rounded-full transition-all flex items-center justify-center"
            aria-label="Mis Liguillas"
        >
            <i class="bi bi-trophy text-lg"></i>
        </a>

        <!-- Torneos Activos / Crear Liga -->
        <a
            href="{{ url('/user/torneos') }}"
            class="p-3 {{ request()->is('user/torneos*') ? 'text-lime-400 bg-lime-400/10' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/80' }} rounded-full transition-all flex items-center justify-center"
            aria-label="Torneos"
        >
            <i class="bi bi-plus-circle text-lg"></i>
        </a>

        <!-- Unirse a Liguilla -->
        <a
            href="{{ url('/user/unirseLiguilla') }}"
            class="p-3 {{ request()->is('user/unirseLiguilla*') ? 'text-lime-400 bg-lime-400/10' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/80' }} rounded-full transition-all flex items-center justify-center"
            aria-label="Unirse a Liguilla"
        >
            <i class="bi bi-bookmark-plus text-lg"></i>
        </a>

        <div class="h-5 w-px bg-zinc-800"></div>

        <!-- Mi Perfil -->
        <a
            href="{{ url('/user/perfil') }}"
            class="p-3 {{ request()->is('user/perfil*') ? 'text-lime-400 bg-lime-400/10' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/80' }} rounded-full transition-all flex items-center justify-center"
            aria-label="Perfil"
        >
            <i class="bi bi-person text-lg"></i>
        </a>

    </div>
</nav>
