@extends('user.layouts.app')

@section('title', 'Torneos Disponibles - MiFantasy')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lime-400/10 border border-lime-400/20 text-lime-400 text-xs font-semibold uppercase tracking-wider mb-2">
                <i class="bi bi-trophy"></i>
                <span>Competiciones Oficiales</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-zinc-100 tracking-tight">
                Torneos Activos
            </h1>
            <p class="text-xs sm:text-sm text-zinc-400 mt-1">
                Selecciona una competición para crear tu liguilla personalizada y competir con amigos.
            </p>
        </div>
    </div>

    <!-- Mensajes de Feedback -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs sm:text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i class="bi bi-exclamation-circle shrink-0"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-lime-500/10 border border-lime-500/20 text-lime-300 text-xs sm:text-sm flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-lime-400 text-base shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Grid de Torneos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($torneos as $torneo)
            <div
                class="card-torneo-item group relative overflow-hidden bg-zinc-900/60 hover:bg-zinc-900/90 border border-zinc-800 hover:border-lime-400/50 rounded-2xl p-5 transition-all duration-300 shadow-lg hover:shadow-lime-400/5 hover:-translate-y-1 cursor-pointer flex flex-col justify-between"
                data-nombre="{{ $torneo->nombre }}"
                data-id="{{ $torneo->id }}"
                data-descripcion="{{ $torneo->descripcion }}"
                data-fecha-inicio="{{ $torneo->fecha_inicio }}"
                data-fecha-fin="{{ $torneo->fecha_fin }}"
                data-num-equipos="{{ count($torneo->equipos) }}"
                data-jug-equipo="{{ $torneo->jugadores_por_equipo }}"
                data-usa-posiciones="{{ $torneo->usa_posiciones }}"
            >
                <!-- Glow decorativo -->
                <div class="absolute -top-12 -right-12 w-28 h-28 bg-lime-400/5 group-hover:bg-lime-400/15 rounded-full blur-2xl transition-all duration-300 pointer-events-none"></div>

                <div>
                    <!-- Logo / Thumbnail -->
                    <div class="w-full h-40 rounded-xl bg-zinc-950/80 border border-zinc-800/80 p-4 flex items-center justify-center overflow-hidden mb-4 relative">
                        @if($torneo->logo)
                            <img src="{{ asset($torneo->logo) }}" alt="{{ $torneo->nombre }}" onerror="this.src='/assets/media/images/default-tournament.png'" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-14 h-14 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-600 group-hover:text-lime-400 transition-colors">
                                <i class="bi bi-trophy text-2xl"></i>
                            </div>
                        @endif

                        <span class="absolute top-2.5 right-2.5 px-2.5 py-0.5 rounded-full bg-zinc-900/90 border border-zinc-800 text-[10px] font-semibold text-zinc-300">
                            {{ count($torneo->equipos) }} Equipos
                        </span>
                    </div>

                    <!-- Datos del Torneo -->
                    <h2 class="text-base font-bold text-zinc-100 group-hover:text-lime-400 transition-colors line-clamp-1">
                        {{ $torneo->nombre }}
                    </h2>
                    <p class="text-xs text-zinc-400 mt-1 line-clamp-2 leading-relaxed">
                        {{ $torneo->descripcion ?? 'Torneo oficial de MiFantasy.' }}
                    </p>
                </div>

                <!-- Footer Card -->
                <div class="pt-4 mt-4 border-t border-zinc-800/80 flex items-center justify-between">
                    <span class="text-[11px] text-zinc-500 flex items-center gap-1.5">
                        <i class="bi bi-people text-zinc-400"></i>
                        <span>{{ $torneo->jugadores_por_equipo }} por alineación</span>
                    </span>
                    <span class="text-xs font-semibold text-lime-400 group-hover:translate-x-0.5 transition-transform inline-flex items-center gap-1">
                        <span>Configurar</span>
                        <i class="bi bi-chevron-right text-[10px]"></i>
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal Configurar Liguilla (Native Tailwind Modal) -->
    <div id="modalLiguilla" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modalLiguillaLabel">
        <div class="relative w-full max-w-lg bg-zinc-950/95 border border-zinc-800 p-6 sm:p-8 rounded-2xl backdrop-blur-xl shadow-2xl space-y-5 my-8">
            <form method="POST" action="{{ url('/user/liguillas/crear') }}" id="formCrearLiguilla">
                @csrf
                <input type="hidden" name="torneo_id" id="modal_torneo_id">

                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-zinc-800/80">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-lime-400/10 border border-lime-400/20 text-lime-400 flex items-center justify-center text-lg">
                            <i class="bi bi-gear-fill"></i>
                        </div>
                        <div>
                            <h5 class="text-base font-bold text-zinc-100 modal-title" id="modalLiguillaLabel">
                                Configurar Liguilla
                            </h5>
                            <p class="text-[11px] text-zinc-400">Personaliza los parámetros de tu nueva competición</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <!-- Botón Info Toggle -->
                        <button
                            id="btnToggleInfoTorneo"
                            class="p-2 rounded-lg text-zinc-400 hover:text-lime-400 hover:bg-zinc-900 border border-transparent hover:border-zinc-800 transition-all cursor-pointer"
                            type="button"
                            title="Ver información del torneo"
                        >
                            <i class="bi bi-info-circle text-base"></i>
                        </button>
                        <!-- Botón Cerrar X -->
                        <button
                            type="button"
                            id="btnCerrarModalX"
                            class="p-2 rounded-lg text-zinc-500 hover:text-zinc-200 hover:bg-zinc-900 transition-colors cursor-pointer"
                            aria-label="Cerrar"
                        >
                            <i class="bi bi-x-lg text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="space-y-4 pt-2">
                    <!-- Info Colapsable -->
                    <div id="torneoInfo" class="hidden">
                        <div class="p-4 rounded-xl bg-zinc-900/90 border border-zinc-800 text-zinc-300 text-xs space-y-2 mb-2">
                            <div class="flex items-center justify-between pb-2 border-b border-zinc-800">
                                <span class="font-semibold text-lime-400 uppercase tracking-wider text-[10px]">Detalles del Torneo</span>
                                <button type="button" id="btnCerrarTorneoInfo" class="text-zinc-400 hover:text-zinc-200 cursor-pointer">
                                    <i class="bi bi-chevron-up text-xs"></i>
                                </button>
                            </div>
                            <div class="grid grid-cols-2 gap-2 pt-1 text-[11px]">
                                <p><strong class="text-zinc-400">Torneo:</strong> <span id="infoTorneoNombre" class="text-zinc-200"></span></p>
                                <p><strong class="text-zinc-400">Equipos:</strong> <span id="infoTorneoNumEquipos" class="text-zinc-200"></span></p>
                                <p><strong class="text-zinc-400">Inicio:</strong> <span id="infoTorneoFechaInicio" class="text-zinc-200"></span></p>
                                <p><strong class="text-zinc-400">Fin:</strong> <span id="infoTorneoFechaFin" class="text-zinc-200"></span></p>
                                <p><strong class="text-zinc-400">Jugadores/alineación:</strong> <span id="infoTorneoJugPorEquipo" class="text-zinc-200"></span></p>
                                <p><strong class="text-zinc-400">Posiciones fijas:</strong> <span id="infoTorneoUsaPosiciones" class="text-zinc-200"></span></p>
                            </div>
                            <p class="pt-1 text-[11px] text-zinc-400 border-t border-zinc-800/60"><strong class="text-zinc-300">Descripción:</strong> <span id="infoTorneoDescripcion"></span></p>
                        </div>
                    </div>

                    <!-- Input Nombre -->
                    <div class="space-y-1.5">
                        <label for="nombre" class="block text-xs font-semibold text-zinc-300">
                            Nombre de la Liguilla <span class="text-lime-400">*</span>
                        </label>
                        <input
                            type="text"
                            name="nombre"
                            id="nombre"
                            class="w-full bg-zinc-900 border border-zinc-800 text-zinc-100 rounded-xl px-4 py-2.5 focus:ring-1 focus:ring-lime-400 focus:border-lime-400 focus:outline-none placeholder-zinc-600 text-sm transition-all"
                            placeholder="Ej. Liga de Amigos 2026"
                            required
                        >
                    </div>

                    <!-- Input Número Máximo de Participantes -->
                    <div class="space-y-1.5">
                        <label for="num_max_part" class="block text-xs font-semibold text-zinc-300">
                            Número máximo de participantes <span class="text-lime-400">*</span>
                        </label>
                        <input
                            type="number"
                            name="num_max_part"
                            id="num_max_part"
                            class="w-full bg-zinc-900 border border-zinc-800 text-zinc-100 rounded-xl px-4 py-2.5 focus:ring-1 focus:ring-lime-400 focus:border-lime-400 focus:outline-none text-sm transition-all"
                            min="2"
                            max="50"
                            value="10"
                            required
                        >
                        <p class="text-[11px] text-zinc-500">Mínimo 2 usuarios por liguilla.</p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="pt-4 mt-6 border-t border-zinc-800/80 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        id="btnCancelarModal"
                        class="px-4 py-2.5 text-xs font-semibold text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900 rounded-xl border border-transparent hover:border-zinc-800 transition-all cursor-pointer"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-lime-400 text-zinc-950 font-bold px-6 py-2.5 rounded-xl hover:bg-lime-300 shadow-lg shadow-lime-400/10 hover:shadow-lime-400/20 active:scale-[0.99] transition-all text-xs sm:text-sm cursor-pointer"
                    >
                        Crear Liguilla
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalLiguilla = document.getElementById('modalLiguilla');
        const cardsTorneo = document.querySelectorAll('.card-torneo-item');
        const btnCerrarModalX = document.getElementById('btnCerrarModalX');
        const btnCancelarModal = document.getElementById('btnCancelarModal');
        const btnToggleInfo = document.getElementById('btnToggleInfoTorneo');
        const btnCerrarInfo = document.getElementById('btnCerrarTorneoInfo');
        const torneoInfo = document.getElementById('torneoInfo');

        function abrirModal(card) {
            if (!modalLiguilla) return;

            const torneoId = card.getAttribute('data-id') || '';
            const torneoNombre = card.getAttribute('data-nombre') || '';
            const torneoDescripcion = card.getAttribute('data-descripcion') || 'Sin descripción disponible.';
            const torneoFechaInicio = card.getAttribute('data-fecha-inicio') || '-';
            const torneoFechaFin = card.getAttribute('data-fecha-fin') || '-';
            const torneoNumEquipos = card.getAttribute('data-num-equipos') || '0';
            const torneoJugPorEquipo = card.getAttribute('data-jug-equipo') || '11';
            const torneoUsaPosiciones = card.getAttribute('data-usa-posiciones') === '1' ? 'Sí' : 'No';

            const inputTorneoId = modalLiguilla.querySelector('#modal_torneo_id');
            const inputNombre = modalLiguilla.querySelector('#nombre');
            const tituloModal = modalLiguilla.querySelector('.modal-title');

            if (inputTorneoId) inputTorneoId.value = torneoId;
            if (inputNombre) {
                inputNombre.value = '';
                setTimeout(() => inputNombre.focus(), 100);
            }
            if (tituloModal) tituloModal.textContent = 'Crear Liguilla de ' + torneoNombre;

            const elNombre = modalLiguilla.querySelector('#infoTorneoNombre');
            const elDesc = modalLiguilla.querySelector('#infoTorneoDescripcion');
            const elInicio = modalLiguilla.querySelector('#infoTorneoFechaInicio');
            const elFin = modalLiguilla.querySelector('#infoTorneoFechaFin');
            const elNumEq = modalLiguilla.querySelector('#infoTorneoNumEquipos');
            const elJugEq = modalLiguilla.querySelector('#infoTorneoJugPorEquipo');
            const elUsaPos = modalLiguilla.querySelector('#infoTorneoUsaPosiciones');

            if (elNombre) elNombre.textContent = torneoNombre;
            if (elDesc) elDesc.textContent = torneoDescripcion;
            if (elInicio) elInicio.textContent = torneoFechaInicio;
            if (elFin) elFin.textContent = torneoFechaFin;
            if (elNumEq) elNumEq.textContent = torneoNumEquipos;
            if (elJugEq) elJugEq.textContent = torneoJugPorEquipo;
            if (elUsaPos) elUsaPos.textContent = torneoUsaPosiciones;

            if (torneoInfo) torneoInfo.classList.add('hidden');

            modalLiguilla.classList.remove('hidden');
            modalLiguilla.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function cerrarModal() {
            if (!modalLiguilla) return;
            modalLiguilla.classList.add('hidden');
            modalLiguilla.classList.remove('flex');
            document.body.style.overflow = '';
        }

        cardsTorneo.forEach(card => {
            card.addEventListener('click', function() {
                abrirModal(this);
            });
        });

        if (btnCerrarModalX) btnCerrarModalX.addEventListener('click', cerrarModal);
        if (btnCancelarModal) btnCancelarModal.addEventListener('click', cerrarModal);

        if (modalLiguilla) {
            modalLiguilla.addEventListener('click', function(e) {
                if (e.target === modalLiguilla) {
                    cerrarModal();
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modalLiguilla && !modalLiguilla.classList.contains('hidden')) {
                cerrarModal();
            }
        });

        if (btnToggleInfo && torneoInfo) {
            btnToggleInfo.addEventListener('click', function(e) {
                e.stopPropagation();
                torneoInfo.classList.toggle('hidden');
            });
        }
        if (btnCerrarInfo && torneoInfo) {
            btnCerrarInfo.addEventListener('click', function(e) {
                e.stopPropagation();
                torneoInfo.classList.add('hidden');
            });
        }
    });
</script>
@endpush
