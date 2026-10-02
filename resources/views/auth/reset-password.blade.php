@extends('auth.layouts.app')

@section('title', 'Restablecer Contraseña - MiFantasy')

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">

        <!-- Header del Formulario -->
        <div class="text-center mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-100">
                Restablecer Contraseña
            </h1>
            <p class="mt-2 text-sm text-zinc-400">
                Crea una nueva contraseña segura para tu cuenta.
            </p>
        </div>

        <!-- Tarjeta Bento Dark -->
        <div class="bg-zinc-900/60 border border-zinc-800/80 backdrop-blur-xl rounded-2xl p-6 sm:p-8 shadow-2xl shadow-zinc-950/50">

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Campo Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                        Correo Electrónico <span class="text-lime-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500">
                            <i class="bi bi-envelope text-base"></i>
                        </div>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email', request()->email) }}"
                            required
                            autofocus
                            placeholder="tu@email.com"
                            class="w-full pl-10 pr-4 py-2.5 bg-zinc-950/80 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-lime-400 focus:ring-1 focus:ring-lime-400 transition-all {{ $errors->has('email') ? '!border-rose-500/60 focus:!ring-rose-500' : '' }}"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Campo Contraseña -->
                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                        Nueva Contraseña <span class="text-lime-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500">
                            <i class="bi bi-lock text-base"></i>
                        </div>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            autocomplete="new-password"
                            placeholder="Mínimo 8 caracteres"
                            class="w-full pl-10 pr-4 py-2.5 bg-zinc-950/80 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-lime-400 focus:ring-1 focus:ring-lime-400 transition-all {{ $errors->has('password') ? '!border-rose-500/60 focus:!ring-rose-500' : '' }}"
                        >
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Campo Confirmación -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                        Confirmar Contraseña <span class="text-lime-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500">
                            <i class="bi bi-shield-check text-base"></i>
                        </div>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Repite la contraseña"
                            class="w-full pl-10 pr-4 py-2.5 bg-zinc-950/80 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-lime-400 focus:ring-1 focus:ring-lime-400 transition-all"
                        >
                    </div>
                </div>

                <!-- Botón CTA -->
                <button
                    type="submit"
                    class="w-full mt-2 py-3 px-4 bg-lime-400 hover:bg-lime-300 text-zinc-950 font-semibold text-sm rounded-xl transition-all shadow-lg shadow-lime-400/10 hover:shadow-lime-400/20 active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer"
                >
                    <span>Cambiar contraseña</span>
                    <i class="bi bi-arrow-right text-base"></i>
                </button>
            </form>

        </div>

    </div>
</div>
@endsection
