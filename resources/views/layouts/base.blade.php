<!DOCTYPE html>
<html lang="es" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#09090b">

    {{-- Meta Descripcion --}}
    <meta name="description" content="@yield('meta_description', 'MiFantasy: Gestiona tus liguillas deportivas, administra tu plantilla y compite con tus amigos en torneos virtuales.')">

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('assets/media/logos/logo-fantasy-nobg.png') }}" type="image/png">

    {{-- Preconnect & Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN & Bootstrap Icons CDN for standard iconography --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            lime: '#a3e635',
                            limeHover: '#bef264',
                            dark: '#09090b',
                            card: '#18181b',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @yield('head')
</head>

<body class="@yield('body_class', 'bg-zinc-950 text-zinc-100 antialiased selection:bg-lime-400 selection:text-zinc-950 min-h-screen flex flex-col')">

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