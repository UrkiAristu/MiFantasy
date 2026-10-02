@extends('auth.layouts.app')

@section('title', 'Verificar Correo - MiFantasy')

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">

        <!-- Header del Formulario -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-lime-400/10 border border-lime-400/20 text-lime-400 mb-4 shadow-lg shadow-lime-400/5">
                <i class="bi bi-envelope-check text-2xl"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-100">
                Verifica tu correo
            </h1>
            <p class="mt-2 text-sm text-zinc-400 leading-relaxed">
                Hemos enviado un enlace de confirmación a <br>
                <span class="font-semibold text-zinc-200">{{ Auth::user()->email ?? 'tu correo electrónico' }}</span>
            </p>
        </div>

        <!-- Tarjeta Bento Dark -->
        <div class="bg-zinc-900/60 border border-zinc-800/80 backdrop-blur-xl rounded-2xl p-6 sm:p-8 shadow-2xl shadow-zinc-950/50 space-y-6">

            @if (session('message') || session('status') == 'verification-link-sent')
                <div class="p-4 rounded-xl bg-lime-500/10 border border-lime-500/20 text-lime-300 text-sm flex items-center gap-2.5">
                    <i class="bi bi-check-circle-fill text-lime-400 text-base shrink-0"></i>
                    <span>{{ session('message') ?? 'Se ha enviado un nuevo enlace de verificación a tu dirección de correo.' }}</span>
                </div>
            @endif

            <p class="text-xs text-zinc-400 leading-relaxed text-center">
                Por favor, revisa tu bandeja de entrada o la carpeta de spam. Si no has recibido el correo, puedes solicitar otro a continuación.
            </p>

            <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
                @csrf
                <button
                    type="submit"
                    class="w-full py-3 px-4 bg-lime-400 hover:bg-lime-300 text-zinc-950 font-semibold text-sm rounded-xl transition-all shadow-lg shadow-lime-400/10 hover:shadow-lime-400/20 active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer"
                >
                    <i class="bi bi-send-fill text-sm"></i>
                    <span>Reenviar correo de verificación</span>
                </button>
            </form>

            <div class="pt-4 border-t border-zinc-800/80 flex items-center justify-between text-xs">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-zinc-400 hover:text-rose-400 transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                        <i class="bi bi-box-arrow-left"></i> Cerrar sesión
                    </button>
                </form>

                <a href="{{ url('/') }}" class="text-zinc-400 hover:text-zinc-200 transition-colors inline-flex items-center gap-1.5">
                    <i class="bi bi-house"></i> Ir al inicio
                </a>
            </div>

        </div>

    </div>
</div>
@endsection
