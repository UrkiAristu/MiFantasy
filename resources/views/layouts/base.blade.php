<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#181c32">

    {{-- Meta Descripcion --}}
    <meta name="description" content="@yield('meta_description', 'MiFantasy: Gestiona tus liguillas deportivas, administra tu plantilla y compite con tus amigos en torneos virtuales.')">

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('assets/media/logos/logo-fantasy-nobg.png') }}" type="image/png">

    {{-- Preconnect & Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    @yield('head')
</head>

<body class="@yield('body_class', '')">

    @yield('body')

    {{-- Global Loading Overlay & Interceptor --}}
    @include('layouts.partials.global-loader')

    {{-- Service Worker (GLOBAL) --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>

    @stack('scripts')
</body>

</html>