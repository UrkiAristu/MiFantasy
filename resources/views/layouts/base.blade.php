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

    @yield('head')
</head>

<body class="@yield('body_class', '')">

    @yield('body')

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