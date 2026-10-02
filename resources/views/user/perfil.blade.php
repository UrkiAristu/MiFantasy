@extends('user.layouts.app')

@section('title', 'Mi Perfil - MiFantasy')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">

    <!-- Header de Página -->
    <div class="space-y-1">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-100">
            Ajustes de Perfil
        </h1>
        <p class="text-sm text-zinc-400">
            Administra tu información personal, credenciales de acceso y preferencias de cuenta.
        </p>
    </div>

    <!-- Bento Card 1: Información del Perfil -->
    <div id="actualizar-perfil" class="bg-zinc-900/60 border border-zinc-800/80 backdrop-blur-xl rounded-2xl p-6 sm:p-8 shadow-2xl shadow-zinc-950/40 space-y-6">

        <div class="flex items-center justify-between pb-4 border-b border-zinc-800/80">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-lime-400/10 border border-lime-400/20 text-lime-400 flex items-center justify-center text-lg">
                    <i class="bi bi-person-vcard"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-zinc-100">Información Personal</h2>
                    <p class="text-xs text-zinc-400">Actualiza tu nombre visible y dirección de correo electrónico.</p>
                </div>
            </div>

            <!-- Estado de Verificación -->
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail)
                @if ($user->hasVerifiedEmail())
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-medium">
                        <i class="bi bi-check-circle-fill text-xs"></i>
                        <span>Verificado</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-medium">
                        <i class="bi bi-exclamation-circle-fill text-xs"></i>
                        <span>No verificado</span>
                    </span>
                @endif
            @endif
        </div>

        {{-- Form oculto para verificación email --}}
        <form id="send-verification" method="post" action="{{ route('user.verification.send') }}">
            @csrf
        </form>

        {{-- Form actualizar perfil --}}
        <form method="post" action="{{ url('/user/perfil/actualizar') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Nombre --}}
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                        Nombre completo <span class="text-lime-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500">
                            <i class="bi bi-person text-base"></i>
                        </div>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="w-full pl-10 pr-4 py-2.5 bg-zinc-950/80 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-lime-400 focus:ring-1 focus:ring-lime-400 transition-all {{ $errors->has('name') ? '!border-rose-500/60 focus:!ring-rose-500' : '' }}"
                        >
                    </div>
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Email --}}
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
                            value="{{ old('email', $user->email) }}"
                            required
                            class="w-full pl-10 pr-4 py-2.5 bg-zinc-950/80 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-lime-400 focus:ring-1 focus:ring-lime-400 transition-all {{ $errors->has('email') ? '!border-rose-500/60 focus:!ring-rose-500' : '' }}"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            @if (session('success'))
                <div class="p-3.5 rounded-xl bg-lime-500/10 border border-lime-500/20 text-lime-300 text-xs flex items-center gap-2">
                    <i class="bi bi-check-circle-fill text-lime-400 text-sm shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                @if (session('status') === 'verification-link-sent')
                    <div class="p-3.5 rounded-xl bg-lime-500/10 border border-lime-500/20 text-lime-300 text-xs flex items-center gap-2">
                        <i class="bi bi-check-circle-fill text-lime-400 text-sm shrink-0"></i>
                        <span>Se ha enviado un nuevo enlace de verificación a tu correo electrónico.</span>
                    </div>
                @endif
            @endif

            <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3">
                <button
                    type="submit"
                    class="w-full sm:w-auto py-2.5 px-5 bg-lime-400 hover:bg-lime-300 text-zinc-950 font-semibold text-xs rounded-xl shadow-lg shadow-lime-400/10 hover:shadow-lime-400/20 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer"
                >
                    <i class="bi bi-check2"></i>
                    <span>Guardar cambios</span>
                </button>

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <button
                        type="button"
                        id="btn-send-verification"
                        class="w-full sm:w-auto py-2.5 px-4 bg-zinc-800/80 hover:bg-zinc-800 text-zinc-300 hover:text-zinc-100 font-semibold text-xs rounded-xl border border-zinc-700/80 transition-all flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <i class="bi bi-envelope-arrow-up"></i>
                        <span>Reenviar email de verificación</span>
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Bento Card 2: Cambiar Contraseña -->
    <div id="actualizar-password" class="bg-zinc-900/60 border border-zinc-800/80 backdrop-blur-xl rounded-2xl p-6 sm:p-8 shadow-2xl shadow-zinc-950/40 space-y-6">

        <div class="flex items-center gap-3 pb-4 border-b border-zinc-800/80">
            <div class="w-10 h-10 rounded-xl bg-lime-400/10 border border-lime-400/20 text-lime-400 flex items-center justify-center text-lg">
                <i class="bi bi-shield-lock"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-zinc-100">Seguridad y Contraseña</h2>
                <p class="text-xs text-zinc-400">Asegúrate de utilizar una contraseña robusta con al menos 8 caracteres.</p>
            </div>
        </div>

        <form method="post" action="{{ url('/user/perfil/password') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                {{-- Contraseña actual --}}
                <div>
                    <label for="current_password" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                        Contraseña actual <span class="text-lime-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500">
                            <i class="bi bi-key text-base"></i>
                        </div>
                        <input
                            type="password"
                            name="current_password"
                            id="current_password"
                            required
                            class="w-full pl-10 pr-4 py-2.5 bg-zinc-950/80 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-lime-400 focus:ring-1 focus:ring-lime-400 transition-all {{ $errors->has('current_password') ? '!border-rose-500/60 focus:!ring-rose-500' : '' }}"
                        >
                    </div>
                    @error('current_password')
                        <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Nueva contraseña --}}
                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                        Nueva contraseña <span class="text-lime-400">*</span>
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
                            class="w-full pl-10 pr-4 py-2.5 bg-zinc-950/80 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-lime-400 focus:ring-1 focus:ring-lime-400 transition-all {{ $errors->has('password') ? '!border-rose-500/60 focus:!ring-rose-500' : '' }}"
                        >
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Confirmación --}}
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                        Confirmar nueva <span class="text-lime-400">*</span>
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
                            class="w-full pl-10 pr-4 py-2.5 bg-zinc-950/80 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-lime-400 focus:ring-1 focus:ring-lime-400 transition-all"
                        >
                    </div>
                </div>
            </div>

            @if (session('password_success'))
                <div class="p-3.5 rounded-xl bg-lime-500/10 border border-lime-500/20 text-lime-300 text-xs flex items-center gap-2">
                    <i class="bi bi-check-circle-fill text-lime-400 text-sm shrink-0"></i>
                    <span>{{ session('password_success') }}</span>
                </div>
            @endif

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full sm:w-auto py-2.5 px-5 bg-zinc-800/80 hover:bg-zinc-800 text-zinc-200 hover:text-zinc-100 font-semibold text-xs rounded-xl border border-zinc-700/80 transition-all flex items-center justify-center gap-2 cursor-pointer"
                >
                    <i class="bi bi-key-fill"></i>
                    <span>Actualizar contraseña</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Bento Card 3: Zona de Peligro (Eliminar Cuenta) -->
    <div id="eliminar-perfil" class="bg-rose-950/20 border border-rose-500/20 backdrop-blur-xl rounded-2xl p-6 sm:p-8 shadow-2xl shadow-zinc-950/40 space-y-4">

        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center text-lg">
                <i class="bi bi-trash3"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-rose-400">Zona de Peligro: Eliminar Cuenta</h2>
                <p class="text-xs text-zinc-400">Una vez que elimines tu cuenta, todas tus ligas y alineaciones se borrarán permanentemente.</p>
            </div>
        </div>

        @if (session('delete_error'))
            <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-sm shrink-0"></i>
                <span>{{ session('delete_error') }}</span>
            </div>
        @endif

        <div class="pt-2">
            <button
                type="button"
                onclick="toggleDeleteModal(true)"
                class="py-2.5 px-5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-300 font-semibold text-xs rounded-xl border border-rose-500/30 transition-all flex items-center gap-2 cursor-pointer"
            >
                <i class="bi bi-exclamation-octagon"></i>
                <span>Eliminar mi cuenta</span>
            </button>
        </div>
    </div>

</div>

<!-- Modal Eliminar Cuenta Tailwind -->
<div id="deleteAccountModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
    <div class="relative w-full max-w-md bg-zinc-900 border border-zinc-800 rounded-2xl p-6 sm:p-8 shadow-2xl shadow-zinc-950 space-y-6">

        <div class="flex items-center justify-between pb-4 border-b border-zinc-800">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-base">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <h3 class="text-base font-bold text-zinc-100">Confirmar Eliminación</h3>
            </div>
            <button type="button" onclick="toggleDeleteModal(false)" class="text-zinc-400 hover:text-zinc-100 p-1.5 rounded-lg hover:bg-zinc-800">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <p class="text-xs text-zinc-400 leading-relaxed">
            Esta acción es irreversible. Para confirmar, por favor introduce tu contraseña actual a continuación.
        </p>

        <form method="post" action="{{ url('/user/perfil/eliminar') }}" class="space-y-4">
            @csrf
            @method('delete')

            <div>
                <label for="password_deletion" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                    Tu Contraseña
                </label>
                <input
                    type="password"
                    name="password_deletion"
                    id="password_deletion"
                    placeholder="Introduce tu contraseña"
                    required
                    class="w-full px-4 py-2.5 bg-zinc-950/80 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-rose-400 focus:ring-1 focus:ring-rose-400 transition-all {{ $errors->userDeletion->has('password_deletion') ? '!border-rose-500' : '' }}"
                >
                @if ($errors->userDeletion->has('password_deletion'))
                    <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                        <i class="bi bi-exclamation-circle"></i> {{ $errors->userDeletion->first('password_deletion') }}
                    </p>
                @endif
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-800">
                <button
                    type="button"
                    onclick="toggleDeleteModal(false)"
                    class="py-2.5 px-4 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-semibold text-xs rounded-xl transition-colors cursor-pointer"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="py-2.5 px-4 bg-rose-600 hover:bg-rose-500 text-white font-semibold text-xs rounded-xl transition-colors shadow-lg shadow-rose-600/20 cursor-pointer"
                >
                    Eliminar definitivamente
                </button>
            </div>
        </form>

    </div>
</div>

@endsection

@push('scripts')
<script>
    function toggleDeleteModal(show) {
        const modal = document.getElementById('deleteAccountModal');
        if (!modal) return;
        if (show) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        @if ($errors->userDeletion->has('password_deletion'))
            toggleDeleteModal(true);
        @endif

        const btn = document.getElementById("btn-send-verification");
        const form = document.getElementById("send-verification");

        if (btn && form) {
            btn.addEventListener("click", function() {
                Swal.fire({
                    title: "¿Enviar email de verificación?",
                    text: "Te enviaremos un nuevo enlace a tu correo electrónico.",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Sí, enviar",
                    cancelButtonText: "Cancelar",
                    confirmButtonColor: "#a3e635",
                    cancelButtonColor: "#27272a",
                    background: "#18181b",
                    color: "#f4f4f5",
                    customClass: {
                        popup: 'border border-zinc-800 rounded-2xl',
                        confirmButton: '!text-zinc-950 font-semibold rounded-xl px-4 py-2',
                        cancelButton: '!text-zinc-300 font-semibold rounded-xl px-4 py-2 border border-zinc-700'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }
    });
</script>
@endpush
