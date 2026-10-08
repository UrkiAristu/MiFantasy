@extends('layouts.base')

@section('head')
<title>@yield('title', 'Panel Torneo - MiFantasy B2B')</title>
@stack('styles')
@endsection

@section('body_class', 'bg-zinc-950 text-zinc-100 min-h-screen flex flex-col font-sans antialiased selection:bg-lime-400 selection:text-zinc-950')

@section('body')
<div class="flex h-screen overflow-hidden bg-zinc-950 text-zinc-100 w-full">
    <!-- Sidebar -->
    <aside class="w-64 bg-zinc-950 border-r border-zinc-800/80 flex flex-col shrink-0">
        <div class="h-16 flex items-center px-6 border-b border-zinc-800/80">
            <a href="{{ route('tenant.dashboard') }}" class="flex items-center gap-2.5 font-bold text-zinc-100">
                <i class="bi bi-hexagon-fill text-lime-400 text-2xl"></i>
                <span>Panel Torneo</span>
            </a>
        </div>
        <nav class="flex-1 p-4 space-y-1.5 text-xs font-medium overflow-y-auto">
            <a href="{{ route('tenant.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-zinc-800/80 text-lime-400 font-semibold">
                <i class="bi bi-speedometer2 text-base text-lime-400"></i>
                <span>Dashboard</span>
            </a>
            <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800/60 transition-colors">
                <i class="bi bi-shield-shaded text-base text-zinc-400"></i>
                <span>Equipos</span>
            </a>
            <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800/60 transition-colors">
                <i class="bi bi-people-fill text-base text-zinc-400"></i>
                <span>Jugadores</span>
            </a>
            <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800/60 transition-colors">
                <i class="bi bi-calendar-event text-base text-zinc-400"></i>
                <span>Jornadas</span>
            </a>
            <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800/60 transition-colors">
                <i class="bi bi-gear-fill text-base text-zinc-400"></i>
                <span>Configuración</span>
            </a>
        </nav>
        <div class="p-4 border-t border-zinc-800/80">
            <a href="http://localhost:8080/home" class="flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-zinc-900 text-zinc-400 hover:text-zinc-100 border border-zinc-800 text-xs transition-colors">
                <i class="bi bi-box-arrow-left"></i>
                <span>Volver al Sitio Principal</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Topbar -->
        <header class="h-16 bg-zinc-950/85 backdrop-blur-xl border-b border-zinc-800/80 px-6 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 rounded-md bg-lime-400/10 text-lime-400 border border-lime-400/20 text-[11px] font-semibold uppercase tracking-wider">
                    Tenant: {{ tenant('name') ?? tenant('id') }}
                </span>
            </div>
            <div class="flex items-center gap-4">
                @auth
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-zinc-900 border border-zinc-800">
                    <div class="w-6 h-6 rounded-full bg-lime-400/20 text-lime-400 flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="text-xs font-semibold text-zinc-200">{{ Auth::user()->name }}</span>
                </div>
                @endauth
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 overflow-y-auto p-6 bg-[#09090b]">
            @yield('content')
        </main>
    </div>
</div>

{{-- Interceptor Global de Notificaciones Toast SweetAlert2 --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#18181b',
                color: '#f4f4f5',
                customClass: {
                    popup: 'border border-zinc-800 rounded-xl shadow-2xl'
                }
            });

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: {!! json_encode(session('success')) !!}
                });
            @endif

            @if(session('status'))
                Toast.fire({
                    icon: 'info',
                    title: {!! json_encode(session('status')) !!}
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: {!! json_encode(session('error')) !!}
                });
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    Toast.fire({
                        icon: 'error',
                        title: {!! json_encode($error) !!}
                    });
                @endforeach
            @endif
        }
    });
</script>
@endsection
