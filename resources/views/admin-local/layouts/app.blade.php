<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin Local - MiFantasy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="h-full flex flex-col font-sans antialiased text-gray-900 bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white text-gray-900 flex flex-col border-r border-gray-200">
            <div class="p-5 text-xl font-bold border-b border-gray-200 flex items-center justify-between">
                <span>MiFantasy Admin</span>
            </div>
            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <a href="/admin-local/dashboard" class="block px-4 py-2 rounded hover:bg-gray-100 text-gray-700 transition font-medium">Dashboard</a>
                <form action="/logout" method="POST" class="mt-8">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 rounded bg-red-600 hover:bg-red-700 transition text-white">Cerrar Sesión</button>
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
</body>
</html>
