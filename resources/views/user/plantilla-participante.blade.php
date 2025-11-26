@extends('user.layouts.app')

@section('title', 'Plantilla de ' . $user->name)

@section('content')
<div class="container my-4">

    {{-- Título --}}
    <div class="text-center mb-4">
        <h2 class="fw-bold">{{ $user->name }}</h2>
        <p class="text-primary">Plantilla en la liguilla: <strong>{{ $liguilla->nombre }}</strong></p>
    </div>

    {{-- Botón volver --}}
    <div class="mb-3">
        <a href="{{ url('/user/liguillas/'.$liguilla->id) }}" class="btn btn-primary">
            <i class="bi bi-arrow-left"></i> Volver a la Liguilla
        </a>
    </div>

    {{-- Card principal --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="card-title mb-3">Plantilla del participante</h4>
            <p class="text-muted mb-4">
                Jugadores: <strong>{{ $plantilla->jugadores->count() }}</strong>
            </p>

            @if($plantilla->jugadores->isEmpty())
                <div class="alert alert-info text-center">
                    Este usuario todavía no tiene jugadores en su plantilla.
                </div>
            @else

            {{-- Grid de jugadores --}}
            <div class="row g-3">
                @foreach($plantilla->jugadores as $jugador)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card jugador-card h-100 text-center p-2" data-jugador-id="{{ $jugador->id }}">

                            <div class="card-body text-center p-2">

                                {{-- Escudo del equipo en ese torneo --}}
                                <div class="jugador-avatar mb-2">
                                    <img src="{{ $jugador->equipoEnTorneo($liguilla->torneo_id)->logo 
                                                ? asset($jugador->equipoEnTorneo($liguilla->torneo_id)->logo)
                                                : asset('assets/media/images/default-team.png') }}"
                                        alt="{{ $jugador->equipoEnTorneo($liguilla->torneo_id)->nombre }}"
                                        class="position-absolute top-0 start-0 m-2"
                                        width="36" height="36"
                                        style="object-fit: contain;">
                                </div>

                                {{-- Foto del jugador --}}
                                <img src="{{ $jugador->foto ? asset($jugador->foto) : asset('assets/media/images/default-player.png') }}"
                                    alt="{{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}"
                                    width="80"
                                    class="rounded-circle mb-2"
                                    style="object-fit: cover; height: 80px;">

                                {{-- Nombre --}}
                                <h3 class="mb-0 fw-bold" style="font-size: 0.95rem;">
                                    {{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}
                                </h3>

                                {{-- Equipo --}}
                                <h5 class="badge bg-primary mt-1">
                                    {{ $jugador->equipoEnTorneo($liguilla->torneo_id)->nombre }}
                                </h5>

                                {{-- Posición --}}
                                <h6 class="text-muted mb-0" style="font-size: 0.85rem;">
                                    {{ $jugador->posicion}}
                                </h6>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Modal Jugador -->
            <div class="modal fade" id="modalJugador" tabindex="-1" aria-labelledby="modalJugadorLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalJugadorLabel">Información del Jugador</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Nombre y foto -->
                            <div class="text-center mb-4">
                                <img id="modalJugadorFoto" src="" alt="Foto jugador" class="rounded-circle mb-3" width="120">
                                <h2 id="modalJugadorNombre" class="fw-bold"></h2>
                            </div>

                            <!-- Estadísticas distribuidas en columnas -->
                            <div class="row text-center">
                                <!-- Columna 1 -->
                                <div class="col-4 mb-3">
                                    <p><strong>Equipo:</strong> <span id="modalJugadorEquipo"></span></p>
                                    <p><strong>Posición:</strong> <span id="modalJugadorPosicion"></span></p>
                                    <p><strong>Edad:</strong> <span id="modalJugadorEdad"></span></p>
                                    <p><strong>Partidos:</strong> <span id="modalJugadorPartidos"></span></p>
                                </div>

                                <!-- Columna 2 -->
                                <div class="col-4 mb-3">
                                    <p><strong>Goles:</strong> <span id="modalJugadorGoles"></span></p>
                                    <p><strong>Asistencias:</strong> <span id="modalJugadorAsistencias"></span></p>
                                    <p><strong>Paradas:</strong> <span id="modalJugadorParadas"></span></p>
                                </div>

                                <!-- Columna 3 -->
                                <div class="col-4 mb-3">
                                    <p><strong>Amarillas:</strong> <span id="modalJugadorAmarillas"></span></p>
                                    <p><strong>Rojas:</strong> <span id="modalJugadorRojas"></span></p>
                                    <p><strong>Faltas:</strong> <span id="modalJugadorFaltas"></span></p>
                                </div>
                            </div>

                            <!-- Puntos al final -->
                            <div class="text-center mt-3">
                                <h4><strong>Puntos:</strong> <span id="modalJugadorPuntos"></span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .jugador-card {
        cursor: pointer;
        transition: transform .2s;
    }

    .jugador-card:hover {
        transform: scale(1.05);
    }
</style>
@endpush
@push('scripts')
<script>
    //Seleccionar jugador en plantilla modal
    document.querySelectorAll('.jugador-card').forEach(card => {
        card.addEventListener('click', function() {
            const idJugador = this.dataset.jugadorId;
            const idTorneo = "{{ $liguilla->torneo_id }}";
            // Aquí puedes hacer un fetch para traer info completa desde Laravel
            fetch(`/user/jugadores/${idJugador}/info/torneo/${idTorneo}`)
                .then(res => res.json())
                .then(data => {
                    // Rellenar modal
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

                    // Mostrar modal
                    const modal = new bootstrap.Modal(document.getElementById('modalJugador'));
                    modal.show();
                })
                .catch(err => {
                    console.error(err);
                    alert('No se pudo cargar la información del jugador.');
                });
        });
    });
    </script>
@endpush
