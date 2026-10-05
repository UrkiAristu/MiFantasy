@extends('user.layouts.app')

@section('title', 'Plantilla de ' . $user->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 text-zinc-200">

    {{-- Título --}}
    <div class="text-center mb-6">
        <h2 class="text-2xl font-extrabold tracking-tight text-zinc-100">{{ $user->name }}</h2>
        <p class="text-lime-400 text-sm mt-1">Plantilla en la liguilla: <strong class="text-zinc-100">{{ $liguilla->nombre }}</strong></p>
    </div>

    {{-- Botón volver --}}
    <div class="mb-6">
        <a href="{{ url('/user/liguillas/'.$liguilla->id) }}" class="inline-flex items-center gap-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-medium py-2 px-4 rounded-xl transition-colors text-sm">
            <i class="bi bi-arrow-left"></i> Volver a la Liguilla
        </a>
    </div>

    {{-- Card principal --}}
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-6 md:p-8 shadow-xl backdrop-blur-sm">

        <h4 class="text-lg font-bold text-zinc-100 mb-1">Plantilla del participante</h4>
        <p class="text-zinc-400 text-sm mb-6">
            Jugadores: <strong class="text-zinc-200">{{ $plantilla->jugadores->count() }}</strong>
        </p>

        @if($plantilla->jugadores->isEmpty())
        <div class="bg-zinc-950/60 border border-zinc-800 rounded-xl p-4 text-center text-zinc-400 text-sm">
            Este usuario todavía no tiene jugadores en su plantilla.
        </div>
        @else

        {{-- Grid de jugadores --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($plantilla->jugadores as $jugador)
            @php
                $equipoTorneo = $jugador->equipoEnTorneo($liguilla->torneo_id);
            @endphp
            <div class="bg-zinc-900/90 border border-zinc-800 rounded-xl p-4 text-center cursor-pointer hover:border-zinc-700 transition-all flex flex-col items-center relative jugador-card group" data-jugador-id="{{ $jugador->id }}">

                {{-- Escudo del equipo en ese torneo --}}
                <div class="absolute top-2 left-2 w-8 h-8 rounded-lg bg-zinc-950/80 border border-zinc-800 p-1 flex items-center justify-center">
                    <img src="{{ $equipoTorneo && $equipoTorneo->logo
                                    ? asset($equipoTorneo->logo)
                                    : asset('assets/media/images/default-team.png') }}"
                        alt="{{ $equipoTorneo ? $equipoTorneo->nombre : 'Sin equipo' }}"
                        class="w-full h-full object-contain"
                        loading="lazy"
                        decoding="async">
                </div>

                {{-- Foto del jugador --}}
                <img src="{{ $jugador->foto ? asset($jugador->foto) : asset('assets/media/images/default-player.png') }}"
                    alt="{{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}"
                    class="w-20 h-20 rounded-full object-cover mb-3 border-2 border-zinc-800 group-hover:border-lime-400/50 transition-colors"
                    loading="lazy"
                    decoding="async">

                {{-- Nombre --}}
                <h3 class="text-sm font-bold text-zinc-100 mb-1 line-clamp-1">
                    {{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}
                </h3>

                {{-- Equipo --}}
                <span class="inline-block px-2 py-0.5 rounded-md bg-lime-400/10 border border-lime-400/20 text-lime-400 text-[11px] font-semibold mb-1">
                    {{ $equipoTorneo ? $equipoTorneo->nombre : 'Sin equipo' }}
                </span>

                {{-- Posición --}}
                <span class="text-zinc-400 text-xs uppercase tracking-wider font-medium">
                    {{ $jugador->posicion }}
                </span>

            </div>
            @endforeach
        </div>

        <!-- Modal Jugador (Tailwind) -->
        <div id="modalJugador" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl relative">
                <div class="flex items-center justify-between p-4 border-b border-zinc-800">
                    <h5 class="text-base font-bold text-zinc-100" id="modalJugadorLabel">Información del Jugador</h5>
                    <button type="button" onclick="cerrarModalJugador()" class="text-zinc-400 hover:text-zinc-100 p-1 rounded-lg hover:bg-zinc-800">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Nombre y foto -->
                    <div class="text-center">
                        <img id="modalJugadorFoto" src="" alt="Foto jugador" class="w-24 h-24 rounded-full object-cover mx-auto mb-3 border-2 border-zinc-800 shadow-md">
                        <h2 id="modalJugadorNombre" class="text-lg font-bold text-zinc-100"></h2>
                    </div>

                    <!-- Estadísticas distribuidas en columnas -->
                    <div class="grid grid-cols-3 gap-4 text-center text-xs bg-zinc-950/60 border border-zinc-800/80 rounded-xl p-4">
                        <div class="space-y-2">
                            <p class="text-zinc-400">Equipo: <span id="modalJugadorEquipo" class="font-bold text-zinc-200 block"></span></p>
                            <p class="text-zinc-400">Posición: <span id="modalJugadorPosicion" class="font-bold text-zinc-200 block"></span></p>
                            <p class="text-zinc-400">Edad: <span id="modalJugadorEdad" class="font-bold text-zinc-200 block"></span></p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-zinc-400">Partidos: <span id="modalJugadorPartidos" class="font-bold text-zinc-200 block"></span></p>
                            <p class="text-zinc-400">Goles: <span id="modalJugadorGoles" class="font-bold text-lime-400 block"></span></p>
                            <p class="text-zinc-400">Asistencias: <span id="modalJugadorAsistencias" class="font-bold text-lime-400 block"></span></p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-zinc-400">Amarillas: <span id="modalJugadorAmarillas" class="font-bold text-amber-400 block"></span></p>
                            <p class="text-zinc-400">Rojas: <span id="modalJugadorRojas" class="font-bold text-red-400 block"></span></p>
                            <p class="text-zinc-400">Puntos: <span id="modalJugadorPuntos" class="font-bold text-lime-400 text-sm block"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
    const cacheJugadores = {};

    function abrirModalJugador() {
        const modal = document.getElementById('modalJugador');
        if (modal) modal.classList.remove('hidden');
    }

    function cerrarModalJugador() {
        const modal = document.getElementById('modalJugador');
        if (modal) modal.classList.add('hidden');
    }

    function renderModalJugador(data) {
        document.getElementById('modalJugadorFoto').src = data.foto || '/assets/media/images/default-player.png';
        document.getElementById('modalJugadorNombre').textContent = `${data.nombre} ${data.apellido1} ${data.apellido2}`;
        document.getElementById('modalJugadorEquipo').textContent = data.equipo;
        document.getElementById('modalJugadorPosicion').textContent = data.posicion || 'Jugador';
        document.getElementById('modalJugadorEdad').textContent = data.edad;
        document.getElementById('modalJugadorPartidos').textContent = data.partidos;
        document.getElementById('modalJugadorGoles').textContent = data.goles;
        document.getElementById('modalJugadorAsistencias').textContent = data.asistencias;
        document.getElementById('modalJugadorParadas').textContent = data.paradas;
        document.getElementById('modalJugadorFaltas').textContent = data.faltas;
        document.getElementById('modalJugadorAmarillas').textContent = data.tarjetas_amarillas;
        document.getElementById('modalJugadorRojas').textContent = data.tarjetas_rojas;
        document.getElementById('modalJugadorPuntos').textContent = data.puntos;

        abrirModalJugador();
    }

    // Seleccionar jugador en plantilla modal
    document.querySelectorAll('.jugador-card').forEach(card => {
        card.addEventListener('click', function() {
            const idJugador = this.dataset.jugadorId;
            const idTorneo = "{{ $liguilla->torneo_id }}";
            const cacheKey = `${idJugador}:${idTorneo}`;

            if (cacheJugadores[cacheKey]) {
                renderModalJugador(cacheJugadores[cacheKey]);
                return;
            }

            fetch(`/user/jugadores/${idJugador}/info/torneo/${idTorneo}`)
                .then(res => res.json())
                .then(data => {
                    cacheJugadores[cacheKey] = data;
                    renderModalJugador(data);
                })
                .catch(err => {
                    console.error(err);
                    alert('No se pudo cargar la información del jugador.');
                });
        });
    });

    // Cerrar con Escape
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalJugador();
        }
    });
</script>
@endpush
