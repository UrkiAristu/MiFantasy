<!DOCTYPE html>
<html lang="es">

<head>
    <title>MiFantasy - Inicio</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('assets/media/logos/logo-fantasy.png') }}" type="image/png">

    <!-- Fuentes y Estilos -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/fantasy.css') }}" rel="stylesheet" />
    <!-- Swiper CSS -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />

    <style>
        .mobile-menu {
            position: fixed;
            top: 0;
            left: -100%;
            width: 250px;
            height: 100%;
            background-color: rgb(0, 0, 0);
            color: white;
            padding: 2rem 1rem;
            transition: left 0.3s ease-in-out;
            z-index: 1050;
        }

        .mobile-menu.show {
            left: 0;
        }

        .mobile-menu a {
            color: white;
            display: block;
            margin: 1rem 0;
            font-weight: 600;
        }

        .hamburger {
            border: none;
            background: none;
            color: white;
            font-size: 1.5rem;
        }

        @media (min-width: 992px) {
            .hamburger {
                display: none;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="bg-body">

    @include('user.layouts.header')

    <main>
        @yield('content')
    </main>

    @include('user.layouts.footer')
</body>

</html>