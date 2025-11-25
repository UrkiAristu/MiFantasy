<!-- Menú lateral móvil -->
<div id="mobileMenu" class="mobile-menu pt-0 bg-primary">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a class="navbar-brand d-flex align-items-center" href="/">
            <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}" alt="MiFantasy Logo" height="40" class="me-2">
            @auth
                {{ Auth::user()->name }}
            @else
                MiFantasy
            @endauth
        </a>
        <button onclick="toggleMenu()" class="btn btn-sm btn-light">
            <i class="bi bi-arrow-left text-dark fs-4"></i>
        </button>
    </div>
    @auth
        @if(Auth::user()->admin)
        <a href="{{ url('/zonaAdmin') }}" class="nav-link text-primary bg-white rounded px-3 mb-2">
            Zona Admin
        </a>
        @endif
        <a href="{{ url('/user/perfil') }}" class="nav-link text-white">Mi Perfil <i class="bi bi-person text-white fs-4 ms-1 align-middle"></i></a>
        <a href="{{ url('/user/liguillas') }}" class="nav-link text-white">Mis Liguillas <i class="bi bi-trophy text-white fs-4 ms-1 align-middle"></i></a>
        <a href="{{ url('/user/torneos') }}" class="nav-link text-white">Torneos Activos <i class="bi bi-plus-circle text-white fs-4 ms-1 align-middle"></i></a>
        <a href="{{ url('/user/unirseLiguilla') }}" class="nav-link text-white">Unirse a Liguilla <i class="bi bi-bookmark-plus text-white fs-4 ms-1 align-middle"></i></a>
        <form action="{{ url('/logout') }}" method="POST" class="mt-3">
            @csrf
            <button type="submit" class="nav-link text-white bg-danger rounded w-100 text-start border-0">
                Cerrar sesión
            </button>
        </form>
    @endauth


</div>

<!-- Navbar superior -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand d-flex align-items-center" href="/">
            <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}" alt="MiFantasy Logo" height="40" class="me-2">
            @auth
                {{ Auth::user()->name }}
            @else
                MiFantasy
            @endauth
        </a>
        <button class="hamburger d-lg-none" onclick="toggleMenu()">☰</button>
        <div class="collapse navbar-collapse justify-content-end d-none d-lg-block">
            <ul class="navbar-nav">
                @auth
                    @if(Auth::user()->admin)
                        <li class="nav-item">
                            <a href="/zonaAdmin" class="nav-link bg-white text-primary rounded mx-3">Zona Admin</a>
                        </li>
                    @endif
                    <li class="nav-item"><a href={{ url('/user/perfil')}} class="nav-link">Mi Perfil</a></li>
                    <li class="nav-item"><a href={{ url('/user/liguillas')}} class="nav-link">Mis Liguillas</a></li>
                    <li class="nav-item"><a href={{ url('/user/torneos')}} class="nav-link">Torneos Activos</a></li>
                    <li class="nav-item"><a href={{ url('/user/unirseLiguilla')}} class="nav-link">Unirse a Liguilla</a></li>
                    <li class="nav-item">
                        <form action="{{ url('/logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link text-danger border-0 bg-transparent">
                                Cerrar sesión
                            </button>
                        </form>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>