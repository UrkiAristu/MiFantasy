<!-- Menú lateral móvil -->
<div id="mobileMenu" class="mobile-menu pt-0">
    <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}" alt="MiFantasy Logo" height="40" class="me-2">
        MiFantasy
    </a>
    @if(session('admin') == 1)
    <a href="#" class="nav-link text-primary bg-white rounded px-3 mb-2">
        Zona Admin
    </a>
    @endif
    <a href="#" class="nav-link text-white">Mis Torneos</a>
    <a href="#" class="nav-link text-white">Mi Equipo</a>
    <a href="#" class="nav-link text-white">Clasificación</a>
    <a href="{{ url('/logout') }}" class="nav-link text-white">Cerrar sesión</a>
    <button onclick="toggleMenu()" class="btn btn-sm btn-light text-dark mb-4">✕ Cerrar</button>

</div>

<!-- Navbar superior -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}" alt="MiFantasy Logo" height="40" class="me-2">
            MiFantasy
        </a>
        <button class="hamburger d-lg-none" onclick="toggleMenu()">☰</button>
        <div class="collapse navbar-collapse justify-content-end d-none d-lg-block">
            <ul class="navbar-nav">
                @if(session('admin') == 1)
                <li class="nav-item">
                    <a href="#" class="nav-link bg-white text-primary rounded mx-3">Zona Admin</a>
                </li>
                @endif
                <li class="nav-item"><a href="#" class="nav-link">Mis Torneos</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Mi Equipo</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Clasificación</a></li>
                <li class="nav-item"><a href="{{ url('/logout') }}" class="nav-link">Cerrar sesión</a></li>
            </ul>
        </div>
    </div>
</nav>