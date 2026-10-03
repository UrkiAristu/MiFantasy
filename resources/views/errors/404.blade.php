@extends('layouts.base')

@section('title', '404 - Página no encontrada | MiFantasy')

@section('body_class', 'bg-zinc-950 text-zinc-100 min-h-screen flex items-center justify-center font-sans antialiased selection:bg-lime-400 selection:text-zinc-950 relative overflow-hidden')

@section('body')
<div class="relative w-full max-w-2xl px-6 py-16 text-center z-10">
    <!-- Glow decorativo de fondo -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-lime-400/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <!-- Badge 404 -->
    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-lime-400/10 border border-lime-400/20 text-lime-400 text-xs font-semibold uppercase tracking-widest mb-6 shadow-sm">
        <i class="bi bi-exclamation-triangle"></i>
        <span>Error 404 • Fuera de Juego</span>
    </div>

    <!-- Título Gigante -->
    <h1 class="text-7xl sm:text-9xl font-black text-transparent bg-clip-text bg-gradient-to-b from-zinc-100 via-zinc-300 to-zinc-600 tracking-tight leading-none mb-4 select-none">
        404
    </h1>

    <!-- Subtítulo -->
    <h2 class="text-xl sm:text-2xl font-bold text-zinc-100 tracking-tight mb-3">
        La jugada ha sido invalidada
    </h2>

    <p class="text-sm sm:text-base text-zinc-400 max-w-md mx-auto mb-8 leading-relaxed">
        La página o plantilla que buscas no existe, ha sido trasladada o no se encuentra disponible en este momento.
    </p>

    <!-- Botón de acción -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="/" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-8 py-3.5 rounded-xl shadow-lg shadow-lime-400/20 hover:shadow-lime-400/30 hover:scale-[1.02] active:scale-[0.98] transition-all text-sm group">
            <i class="bi bi-house-door-fill text-base group-hover:-translate-y-0.5 transition-transform"></i>
            <span>Volver al inicio</span>
        </a>
    </div>

    <!-- Footer sutil -->
    <div class="mt-12 text-xs text-zinc-600">
        MiFantasy &copy; {{ date('Y') }} — Todos los derechos reservados.
    </div>
</div>
@endsection
