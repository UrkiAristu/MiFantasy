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
        <form action="{{ url('/logout') }}" method="POST" class="mt-3">
            @csrf
            <button type="submit" class="nav-link text-white bg-danger rounded w-100 text-start border-0">
                Cerrar sesión
            </button>
        </form>
    @endauth


</div>

<!-- Navbar superior -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
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