@extends('auth.layouts.app')

@section('title', 'Crear Cuenta - MiFantasy')

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-xl">

        <!-- Header del Formulario -->
        <div class="text-center mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-100">
                Únete a la competición
            </h1>
            <p class="mt-2 text-sm text-zinc-400">
                ¿Ya tienes una cuenta activa?
                <a href="{{ url('/login') }}" class="font-medium text-lime-400 hover:text-lime-300 transition-colors">
                    Inicia sesión
                </a>
            </p>
        </div>

        <!-- Tarjeta de Registro (Bento Dark) -->
        <div class="bg-zinc-900/60 border border-zinc-800/80 backdrop-blur-xl rounded-2xl p-6 sm:p-8 shadow-2xl shadow-zinc-950/50">

            {{-- Errores Generales --}}
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm">
                    <div class="flex items-center gap-2 font-semibold mb-1">
                        <i class="bi bi-exclamation-circle-fill text-rose-400"></i>
                        <span>Revisa los siguientes campos:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-xs text-rose-300/90 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $selectedPlan = request()->query('plan', old('plan'));
                $plansConfig = config('saas.plans', []);
                $planInfo = $plansConfig[$selectedPlan] ?? null;
            @endphp

            <form method="POST" action="{{ url('/registro') }}" id="formularioRegistro" class="space-y-5">
                @csrf

                {{-- Banner de Plan SaaS Seleccionado --}}
                @if ($planInfo)
                    <div class="p-4 rounded-xl bg-lime-500/10 border border-lime-500/30 text-zinc-200">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-bold text-lime-400 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="bi bi-stars"></i> Plan Seleccionado: {{ $planInfo['name'] }}
                            </span>
                            <span class="px-2.5 py-0.5 text-xs font-bold bg-lime-400 text-zinc-950 rounded-lg shadow-sm">
                                €{{ $planInfo['price'] }}/mes
                            </span>
                        </div>
                        <p class="text-xs text-zinc-400 leading-relaxed">
                            {{ $planInfo['description'] }}. Al registrarte se iniciará automáticamente el aprovisionamiento de tu liga.
                        </p>
                    </div>
                    <input type="hidden" name="plan" value="{{ $selectedPlan }}">

                    <div>
                        <label for="organizacion" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                            Nombre de tu Liga / Organización <span class="text-lime-400">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500">
                                <i class="bi bi-trophy text-base"></i>
                            </div>
                            <input
                                type="text"
                                id="organizacion"
                                name="organizacion"
                                placeholder="Ej: Liga Municipal Norte, Torneo Premier..."
                                value="{{ old('organizacion') }}"
                                required
                                class="w-full pl-10 pr-4 py-2.5 bg-zinc-950/80 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-lime-400 focus:ring-1 focus:ring-lime-400 transition-all {{ $errors->has('organizacion') ? '!border-rose-500/60' : '' }}"
                            >
                        </div>
                        <p class="text-[11px] text-zinc-500 mt-1.5">Nombre del espacio de trabajo exclusivo para tu liga y administradores.</p>
                    </div>
                @endif

                <!-- Nombre de Usuario -->
                <div>
                    <label for="nombreUsuario" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                        Nombre de Usuario <span class="text-lime-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500">
                            <i class="bi bi-person text-base"></i>
                        </div>
                        <input
                            type="text"
                            id="nombreUsuario"
                            name="nombreUsuario"
                            value="{{ old('nombreUsuario') }}"
                            required
                            autofocus
                            placeholder="tu_alias_manager"
                            class="w-full pl-10 pr-4 py-2.5 bg-zinc-950/80 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-lime-400 focus:ring-1 focus:ring-lime-400 transition-all {{ $errors->has('nombreUsuario') ? '!border-rose-500/60' : '' }}"
                        >
                    </div>
                </div>

                <!-- Correo Electrónico -->
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
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="tu@email.com"
                            class="w-full pl-10 pr-4 py-2.5 bg-zinc-950/80 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-lime-400 focus:ring-1 focus:ring-lime-400 transition-all {{ $errors->has('email') ? '!border-rose-500/60' : '' }}"
                        >
                    </div>
                </div>

                <!-- Grid de Contraseñas -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                            Contraseña <span class="text-lime-400">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500">
                                <i class="bi bi-lock text-base"></i>
                            </div>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="Mínimo 8 caracteres"
                                class="w-full pl-10 pr-4 py-2.5 bg-zinc-950/80 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-lime-400 focus:ring-1 focus:ring-lime-400 transition-all {{ $errors->has('password') ? '!border-rose-500/60' : '' }}"
                            >
                        </div>
                    </div>

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
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Repite la contraseña"
                                class="w-full pl-10 pr-4 py-2.5 bg-zinc-950/80 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-lime-400 focus:ring-1 focus:ring-lime-400 transition-all"
                            >
                        </div>
                    </div>
                </div>

                <!-- Mostrar Contraseñas -->
                <div class="flex items-center gap-2 pt-1">
                    <input
                        type="checkbox"
                        id="mostrar_contraseña"
                        onclick="togglePassword()"
                        class="w-4 h-4 rounded border-zinc-700 bg-zinc-950 text-lime-400 focus:ring-lime-400 focus:ring-offset-zinc-900 accent-lime-400 cursor-pointer"
                    >
                    <label for="mostrar_contraseña" class="text-xs text-zinc-400 cursor-pointer select-none font-medium">
                        Mostrar contraseñas en texto claro
                    </label>
                </div>

                <!-- Botón de Registro -->
                <button
                    type="submit"
                    class="w-full mt-4 py-3 px-4 bg-lime-400 hover:bg-lime-300 text-zinc-950 font-semibold text-sm rounded-xl transition-all shadow-lg shadow-lime-400/10 hover:shadow-lime-400/20 active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer"
                >
                    <span>Crear cuenta y comenzar</span>
                    <i class="bi bi-arrow-right text-base"></i>
                </button>
            </form>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword() {
        const fields = ['password', 'password_confirmation'];
        fields.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
            }
        });
    }
</script>
@endpush
