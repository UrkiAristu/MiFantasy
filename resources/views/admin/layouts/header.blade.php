<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/zonaAdmin') }}">
            <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}" alt="Logo" height="40" class="me-2">
            Admin
            @auth
            {{ Auth::user()->name}}
            @else
            Panel
            @endauth
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link bg-white text-primary rounded mx-3 px-2" href="{{ url('/') }}">Zona User</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/admin/usuarios') }}">Usuarios</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/admin/torneos') }}">Torneos</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/admin/equipos') }}">Equipos</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/admin/jugadores') }}">Jugadores</a></li>
                <li class="nav-item">
                    <form action="{{ url('/logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link text-danger border-0 bg-transparent">
                            Cerrar sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>