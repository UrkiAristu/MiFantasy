@extends('auth.layouts.app')

@section('title', 'Iniciar Sesión - MiFantasy')

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">

        <!-- Header del Formulario -->
        <div class="text-center mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-100">
                Bienvenido de nuevo
            </h1>
            <p class="mt-2 text-sm text-zinc-400">
                ¿Aún no tienes cuenta?
                <a href="{{ url('/registro') }}" class="font-medium text-lime-400 hover:text-lime-300 transition-colors">
                    Regístrate gratis
                </a>
            </p>
        </div>

        <!-- Tarjeta de Login (Bento Dark) -->
        <div class="bg-zinc-900/60 border border-zinc-800/80 backdrop-blur-xl rounded-2xl p-6 sm:p-8 shadow-2xl shadow-zinc-950/50">

            {{-- Errores de Validación --}}
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm">
                    <div class="flex items-center gap-2 font-semibold mb-1">
                        <i class="bi bi-exclamation-circle-fill text-rose-400"></i>
                        <span>No pudimos iniciar sesión</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-xs text-rose-300/90 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-lime-500/10 border border-lime-500/20 text-lime-300 text-sm flex items-center gap-2">
                    <i class="bi bi-check-circle-fill text-lime-400"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ url('/login') }}" id="formularioLogin" class="space-y-5">
                @csrf

                <!-- Campo Login (Usuario / Email) -->
                <div>
                    <label for="login" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                        Usuario o Correo Electrónico
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500">
                            <i class="bi bi-person text-base"></i>
                        </div>
                        <input
                            type="text"
                            name="login"
                            id="login"
                            value="{{ old('login') }}"
                            required
                            autofocus
                            placeholder="tu_usuario o correo@ejemplo.com"
                            class="w-full pl-10 pr-4 py-2.5 bg-zinc-950/80 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-lime-400 focus:ring-1 focus:ring-lime-400 transition-all {{ $errors->has('login') ? '!border-rose-500/60 focus:!ring-rose-500' : '' }}"
                        >
                    </div>
                </div>

                <!-- Campo Contraseña -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300">
                            Contraseña
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-medium text-zinc-400 hover:text-lime-400 transition-colors">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500">
                            <i class="bi bi-lock text-base"></i>
                        </div>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-2.5 bg-zinc-950/80 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-lime-400 focus:ring-1 focus:ring-lime-400 transition-all {{ $errors->has('password') || $errors->has('login') ? '!border-rose-500/60 focus:!ring-rose-500' : '' }}"
                        >
                    </div>
                </div>

                <!-- Recordarme -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-zinc-700 bg-zinc-950 text-lime-400 focus:ring-lime-400 focus:ring-offset-zinc-900 accent-lime-400"
                        >
                        <span class="text-xs text-zinc-400 font-medium">Recordarme en este equipo</span>
                    </label>
                </div>

                <!-- Botón de Envío -->
                <button
                    type="submit"
                    class="w-full mt-2 py-3 px-4 bg-lime-400 hover:bg-lime-300 text-zinc-950 font-semibold text-sm rounded-xl transition-all shadow-lg shadow-lime-400/10 hover:shadow-lime-400/20 active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer"
                >
                    <span>Entrar al juego</span>
                    <i class="bi bi-arrow-right text-base"></i>
                </button>
            </form>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    window.addEventListener('load', function() {
        try {
            window.ReactNativeWebView?.postMessage(JSON.stringify({
                accion: "dispId"
            }));
            window.ReactNativeWebView?.postMessage(JSON.stringify({
                accion: "bioLogin"
            }));
        } catch (e) {}
    });
</script>
@endpush
