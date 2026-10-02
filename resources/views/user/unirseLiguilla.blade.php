@extends('user.layouts.app')

@section('title', 'Unirse a Liguilla - MiFantasy')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20">

    <!-- Card Principal Bento -->
    <div class="relative overflow-hidden bg-zinc-900/60 border border-zinc-800/80 backdrop-blur-xl rounded-3xl p-6 sm:p-10 shadow-2xl shadow-zinc-950/60 text-center space-y-8">

        <!-- Glow ambiental decorativo -->
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-lime-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Encabezado con Icono -->
        <div class="relative z-10 space-y-3">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-lime-400/10 border border-lime-400/20 text-lime-400 flex items-center justify-center text-3xl shadow-lg shadow-lime-400/5">
                <i class="bi bi-bookmark-plus"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-zinc-100">
                Unirse a una Liguilla
            </h1>
            <p class="text-xs sm:text-sm text-zinc-400 max-w-md mx-auto leading-relaxed">
                Introduce el código de 8 caracteres alfanuméricos proporcionado por el administrador para empezar a competir.
            </p>
        </div>

        <form action="{{ url('/user/liguillas/unirse') }}" method="POST" class="relative z-10 space-y-6">
            @csrf

            <!-- Campo Código Grande con botón pegar -->
            <div class="space-y-2 text-left">
                <label for="codigo" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 text-center">
                    Código de Invitación <span class="text-lime-400">*</span>
                </label>

                <div class="relative">
                    <input
                        type="text"
                        name="codigo"
                        id="codigo"
                        value="{{ old('codigo', $codigo) }}"
                        class="w-full text-center text-2xl sm:text-3xl font-mono font-bold tracking-[0.3em] uppercase py-4 px-12 bg-zinc-950/90 border border-zinc-800 focus:border-lime-400 rounded-2xl text-zinc-100 placeholder-zinc-700 focus:outline-none focus:ring-2 focus:ring-lime-400/20 transition-all shadow-inner {{ $errors->any() ? '!border-rose-500/60 focus:!ring-rose-500/20' : '' }}"
                        placeholder="ABC12345"
                        maxlength="8"
                        required
                        autofocus
                    >

                    <!-- Botón Pegar Portapapeles -->
                    <button
                        type="button"
                        id="btnPegar"
                        class="absolute right-3 top-1/2 -translate-y-1/2 p-2.5 rounded-xl bg-zinc-800/80 hover:bg-zinc-800 text-zinc-400 hover:text-lime-400 border border-zinc-700/80 transition-all flex items-center justify-center cursor-pointer group"
                        title="Pegar del portapapeles"
                        aria-label="Pegar código del portapapeles"
                    >
                        <i class="bi bi-clipboard text-base group-hover:scale-110 transition-transform"></i>
                    </button>
                </div>

                <p class="text-center text-[11px] text-zinc-500">
                    Formato de 8 letras o números (Ejemplo: <span class="font-mono text-zinc-400">XK92PL01</span>)
                </p>
            </div>

            <!-- Feedback Mensajes -->
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-lime-500/10 border border-lime-500/20 text-lime-300 text-xs flex items-center justify-center gap-2">
                    <i class="bi bi-check-circle-fill text-lime-400 text-sm shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs flex items-center justify-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-sm shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs text-left space-y-1">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-1.5">
                            <i class="bi bi-exclamation-circle shrink-0"></i>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Botón CTA -->
            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full py-3.5 px-6 bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold text-sm rounded-xl shadow-lg shadow-lime-400/10 hover:shadow-lime-400/20 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer"
                >
                    <i class="bi bi-box-arrow-in-right text-base font-bold"></i>
                    <span>Unirme a la Liguilla</span>
                </button>
            </div>
        </form>

        <div class="pt-4 border-t border-zinc-800/80 flex items-center justify-center gap-4 text-xs text-zinc-400">
            <span>¿Quieres organizar una nueva?</span>
            <a href="{{ url('/user/torneos') }}" class="text-lime-400 hover:text-lime-300 font-semibold transition-colors flex items-center gap-1">
                <span>Crear liguilla</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    const inputCodigo = document.getElementById('codigo');
    const btnPegar = document.getElementById('btnPegar');

    if (inputCodigo) {
        inputCodigo.addEventListener('input', function() {
            this.value = this.value
                .toUpperCase()
                .replace(/[^A-Z0-9]/g, '')
                .slice(0, 8);
        });
    }

    if (btnPegar && inputCodigo) {
        btnPegar.addEventListener('click', async () => {
            if (navigator.clipboard) {
                try {
                    let texto = await navigator.clipboard.readText();
                    texto = texto.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 8);
                    inputCodigo.value = texto;
                    inputCodigo.focus();
                    inputCodigo.setSelectionRange(texto.length, texto.length);
                } catch (e) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Permiso requerido',
                        text: 'Por favor, pega el código manualmente con Ctrl+V.',
                        background: '#18181b',
                        color: '#f4f4f5',
                        customClass: { popup: 'border border-zinc-800 rounded-2xl' }
                    });
                }
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'No soportado',
                    text: 'Pega el código manualmente.',
                    background: '#18181b',
                    color: '#f4f4f5',
                    customClass: { popup: 'border border-zinc-800 rounded-2xl' }
                });
            }
        });
    }
</script>
@endpush
