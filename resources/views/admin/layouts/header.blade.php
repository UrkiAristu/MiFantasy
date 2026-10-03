<nav class="navbar navbar-expand-lg navbar-dark bg-zinc-950 border-b border-zinc-800 shadow-sm sticky-top">
    <div class="container-fluid flex items-center justify-between">
        <a class="navbar-brand d-flex align-items-center gap-4" href="{{ url('/zonaAdmin') }}">
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

        <!-- Botón PC -->
        <a href="{{ url('/') }}" class="hidden md:block bg-lime-400 hover:bg-lime-500 text-zinc-950 px-4 py-2 rounded-lg text-sm font-bold transition-colors">
            Volver a la App
        </a>

        <button class="navbar-toggler md:hidden p-2 text-zinc-400 hover:text-zinc-100 focus:outline-none border border-zinc-800 rounded-lg" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse absolute w-full left-0 top-full bg-zinc-900 z-50 shadow-2xl p-4 md:static md:w-auto md:bg-transparent md:z-auto md:shadow-none md:p-0" id="adminNavbar">
            <ul class="navbar-nav ms-auto align-items-start md:align-items-center gap-1">
                <li class="nav-item w-full md:w-auto">
                    <a class="nav-link text-zinc-200 hover:text-white px-3 py-2 rounded-lg hover:bg-zinc-800 transition-colors block" href="{{ url('/zonaAdmin') }}">Panel de Inicio</a>
                </li>
                <li class="nav-item w-full md:w-auto">
                    <a class="nav-link text-zinc-200 hover:text-white px-3 py-2 rounded-lg hover:bg-zinc-800 transition-colors block" href="{{ url('/admin/usuarios') }}">Usuarios</a>
                </li>
                <li class="nav-item w-full md:w-auto">
                    <a class="nav-link text-zinc-200 hover:text-white px-3 py-2 rounded-lg hover:bg-zinc-800 transition-colors block" href="{{ url('/admin/liguillas') }}">Liguillas</a>
                </li>
                <li class="nav-item w-full md:w-auto">
                    <a class="nav-link text-zinc-200 hover:text-white px-3 py-2 rounded-lg hover:bg-zinc-800 transition-colors block" href="{{ url('/admin/torneos') }}">Torneos</a>
                </li>
                <li class="nav-item w-full md:w-auto">
                    <a class="nav-link text-zinc-200 hover:text-white px-3 py-2 rounded-lg hover:bg-zinc-800 transition-colors block" href="{{ url('/admin/equipos') }}">Equipos</a>
                </li>
                <li class="nav-item w-full md:w-auto">
                    <a class="nav-link text-zinc-200 hover:text-white px-3 py-2 rounded-lg hover:bg-zinc-800 transition-colors block" href="{{ url('/admin/jugadores') }}">Jugadores</a>
                </li>
                <li class="nav-item w-full md:w-auto">
                    <a class="nav-link bg-lime-400 hover:bg-lime-500 text-zinc-950 font-bold px-3 py-2 rounded-lg transition-colors block md:hidden mt-2" href="{{ url('/') }}">Volver a la App</a>
                </li>
                <li class="nav-item w-full md:w-auto mt-2 md:mt-0">
                    <form action="{{ url('/logout') }}" method="POST" class="d-block md:d-inline">
                        @csrf
                        <button type="submit" class="nav-link text-rose-400 hover:text-rose-300 border-0 bg-transparent w-full text-start md:text-center px-3 py-2 rounded-lg hover:bg-zinc-800 transition-colors">
                            Cerrar sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>