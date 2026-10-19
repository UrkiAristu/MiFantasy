<!DOCTYPE html>
<html lang="es" class="h-full bg-zinc-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin Local - MiFantasy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="h-full flex flex-col font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-zinc-800 text-white flex flex-col border-r border-zinc-700">
            <div class="p-5 text-xl font-bold border-b border-zinc-700 flex items-center justify-between">
                <span>MiFantasy Admin</span>
            </div>
            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <a href="/admin-local/dashboard" class="block px-4 py-2 rounded hover:bg-zinc-700 transition">Dashboard</a>
                <form action="/logout" method="POST" class="mt-8">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 rounded bg-red-600 hover:bg-red-700 transition text-white">Cerrar Sesión</button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 bg-zinc-800 border-b border-zinc-700 flex items-center justify-between px-6">
                <span class="text-zinc-300">Panel de Control Local</span>
                <span class="text-zinc-400 text-sm">{{ Auth::user()->name ?? 'Administrador' }}</span>
            </header>
            <main class="flex-1 overflow-y-auto p-6 bg-zinc-900">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
