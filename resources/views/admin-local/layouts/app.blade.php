<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin Local - MiFantasy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        #global-loader {
            transition: opacity 0.2s ease;
        }
    </style>
</head>
<body class="h-full flex flex-col font-sans antialiased text-gray-900 bg-gray-50">
    <!-- Loader Global -->
    <div id="global-loader" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center opacity-0 pointer-events-none">
        <div class="bg-white px-6 py-4 rounded-xl shadow-xl flex items-center gap-3">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-emerald-600"></div>
            <span class="text-sm font-semibold text-gray-700">Cambiando contexto de torneo...</span>
        </div>
    </div>
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white text-gray-900 flex flex-col border-r border-gray-200">
            <div class="p-5 text-xl font-bold border-b border-gray-200 flex items-center justify-between">
                <span>MiFantasy Admin</span>
            </div>
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <a href="{{ route('admin.local.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.local.dashboard') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-100 font-medium' }} transition">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('admin.local.equipos.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.local.equipos*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-100 font-medium' }} transition">
                    <i class="bi bi-shield-shaded"></i> Equipos
                </a>
                <a href="{{ route('admin.local.jornadas.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.local.jornadas*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-100 font-medium' }} transition">
                    <i class="bi bi-calendar-plus"></i> Jornadas
                </a>
                <a href="{{ route('admin.local.partidos.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.local.partidos*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-100 font-medium' }} transition">
                    <i class="bi bi-controller"></i> Partidos
                </a>

                <form action="/logout" method="POST" class="mt-8">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 font-medium transition">
                        <i class="bi bi-box-arrow-right"></i> Cerrar SesiÃ³n
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
                <span class="text-gray-900 font-semibold">Panel de Control Local</span>
                <span class="text-gray-500 text-sm">{{ Auth::user()->name ?? 'Administrador' }}</span>
            </header>
            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
                @yield('content')
            </main>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loader = document.getElementById('global-loader');

            const activateLoader = () => {
                loader.classList.remove('opacity-0', 'pointer-events-none');
                loader.classList.add('opacity-100');
            };

            const selector = document.querySelector('select[name="torneo_id"]');
            if (selector) {
                selector.addEventListener('change', activateLoader);
            }

            document.querySelectorAll('a[href*="/admin-local/dashboard"]').forEach(link => {
                link.addEventListener('click', activateLoader);
            });
        });
    </script>

    @if(session('success'))
    <script>
        Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        }).fire({
            icon: 'success',
            title: "{{ session('success') }}"
        });
    </script>
    @endif
    @if(session('error'))
    <script>
        Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        }).fire({
            icon: 'error',
            title: "{{ session('error') }}"
        });
    </script>
    @endif
</body>
</html>

