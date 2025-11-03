<!-- Menú lateral móvil -->
<div id="mobileMenu" class="mobile-menu pt-0 bg-primary">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a class="navbar-brand d-flex align-items-center" href="/">
            <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}" alt="MiFantasy Logo" height="40" class="me-2">
            @if (session()->has('nombreUsuario'))
            {{ session('nombreUsuario') }}
            @else
            MiFantasy
            @endif
        </a>
        <button onclick="toggleMenu()" class="btn btn-sm btn-light">
            <i class="bi bi-arrow-left text-dark fs-4"></i>
        </button>
    </div>
    @if(session('admin') == 1)
    <a href="{{ url('/zonaAdmin') }}" class="nav-link text-primary bg-white rounded px-3 mb-2">
        Zona Admin
    </a>
    @endif
    <a href="{{ url('/user/liguillas') }}" class="nav-link text-white">Mis Liguillas</a>
    <a href="{{ url('/user/torneos') }}" class="nav-link text-white">Torneos Activos</a>
    <a href="{{ url('/user/unirseLiguilla') }}" class="nav-link text-white">Unirse a Liguilla</a>
    <a href="{{ url('/logout') }}" class="nav-link text-white bg-danger rounded">Cerrar sesión</a>


</div>

<!-- Navbar superior -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand d-flex align-items-center" href="/">
            <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}" alt="MiFantasy Logo" height="40" class="me-2">
            @if(session()->has('nombreUsuario'))
            {{ session('nombreUsuario') }}
            @else
            MiFantasy
            @endif
        </a>
        <button class="hamburger d-lg-none" onclick="toggleMenu()">☰</button>
        <div class="collapse navbar-collapse justify-content-end d-none d-lg-block">
            <ul class="navbar-nav">
                @if(session('admin') == 1)
                <li class="nav-item">
                    <a href="/zonaAdmin" class="nav-link bg-white text-primary rounded mx-3">Zona Admin</a>
                </li>
                @endif
                <li class="nav-item"><a href={{ url('/user/liguillas')}} class="nav-link">Mis Liguillas</a></li>
                <li class="nav-item"><a href={{ url('/user/torneos')}} class="nav-link">Torneos Activos</a></li>
                <li class="nav-item"><a href={{ url('/user/unirseLiguilla')}} class="nav-link">Unirse a Liguilla</a></li>
                <li class="nav-item"><a href={{ url('/logout') }} class="nav-link text-danger">Cerrar sesión</a></li>
            </ul>
        </div>
    </div>
</nav>