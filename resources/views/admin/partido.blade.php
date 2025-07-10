@extends('admin.layouts.app')

@section('title', 'Detalle del Partido')

@section('content')
<div class="container mt-5">
    <!-- Título -->
    <div class="text-center mb-4">
        <h1>Detalle del Partido</h1>
        <a href="{{ url('/admin/torneos/'.$partido->torneo->id.'/partidos') }}">
            <h4>{{ $partido->torneo->nombre }}</h4>
        </a>
    </div>

    <!-- Equipos y marcador -->
    <div class="row align-items-center text-center mb-5">
        <div class="col-md-5">
            @if($partido->equipoLocal->logo)
            <img src="{{ asset($partido->equipoLocal->logo) }}" alt="Logo Local" class="img-fluid mb-2" style="max-height: 100px;">
            @endif
            <h3>{{ $partido->equipoLocal->nombre }}</h3>
        </div>
        <div class="col-md-2">
            <h2 class="fw-bold">{{ $partido->goles_local ?? '-' }} - {{ $partido->goles_visitante ?? '-' }}</h2>
            <p class="text-muted">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('d/m/Y') }} {{ \Carbon\Carbon::parse($partido->fecha_partido)->format('H:i') }}</p>
        </div>
        <div class="col-md-5">
            @if($partido->equipoVisitante->logo)
            <img src="{{ asset($partido->equipoVisitante->logo) }}" alt="Logo Visitante" class="img-fluid mb-2" style="max-height: 100px;">
            @endif
            <h3>{{ $partido->equipoVisitante->nombre }}</h3>
        </div>
    </div>
    <!-- Mensajes -->
    @if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
        {{ $error }}<br>
        @endforeach
    </div>
    @endif
    <!-- Eventos (Timeline) -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Cronología de Eventos</h5>
                <div>
                    <!-- Botón Gestionar Eventos -->
                    <button type="button"
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#eventosPartidoModal"
                        data-partido-id="{{ $partido->id }}"
                        data-eventos='@json($partido->eventos ?? [])'>
                        Gestionar Eventos
                    </button>
                    <!-- Botón Editar Partido -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editarPartidoModal">
                        Editar Partido
                    </button>
                    <a href="{{ url('/admin/partidos/'.$partido->id.'/eliminar') }}"
                        class="btn btn-danger btn-eliminar-partido"
                        title="Eliminar el partido"
                        data-url="{{ url('/admin/partidos/'.$partido->id.'/eliminar') }}">
                        Eliminar
                    </a>
                </div>
            </div>

            @php
            $eventos = json_decode($partido->eventos);
            $eventos = collect($eventos)->sortBy('minuto'); // ordenar por minuto
            @endphp

            @if($eventos && $eventos->count())
            <ul class="list-group">
                @foreach($eventos as $evento)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <strong>{{ $evento->minuto }}'</strong> -
                        {{ $evento->tipo }}
                        ({{ ucfirst($evento->equipo) }})
                        @if(!empty($evento->jugador_nombre))
                        - {{ $evento->jugador_nombre }}
                        @endif
                    </span>
                    @if($evento->tipo == 'Gol')
                    <i class="bi bi-soccer fs-5 text-success"></i>
                    @elseif(str_contains($evento->tipo, 'Tarjeta'))
                    <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                    @elseif($evento->tipo == 'Cambio')
                    <i class="bi bi-arrow-repeat fs-5 text-info"></i>
                    @else
                    <i class="bi bi-info-circle fs-5 text-secondary"></i>
                    @endif
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-muted mb-0">No hay eventos registrados para este partido.</p>
            @endif
        </div>
    </div>

    <!-- Botón para volver -->
    <div class="mt-4">
        <a href="{{ url('/admin/torneos/'.$partido->torneo->id) }}" class="btn btn-secondary">Volver al Torneo</a>
    </div>
</div>
<!-- Modal Editar Partido -->
<div class="modal fade" id="editarPartidoModal" tabindex="-1" aria-labelledby="editarPartidoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ url('/admin/partidos/'.$partido->id.'/editar') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="editarPartidoModalLabel">Editar Partido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <!-- Equipo Local -->
                    <div class="col-md-6 mb-3">
                        <label for="editarEquipoLocal" class="form-label">Equipo Local</label>
                        <select name="equipo_local_id" id="editarEquipoLocal" class="form-select" required>
                            @foreach($equipos as $equipo)
                            <option value="{{ $equipo->id }}"
                                {{ $partido->equipo_local_id == $equipo->id ? 'selected' : '' }}>
                                {{ $equipo->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Equipo Visitante -->
                    <div class="col-md-6 mb-3">
                        <label for="editarEquipoVisitante" class="form-label">Equipo Visitante</label>
                        <select name="equipo_visitante_id" id="editarEquipoVisitante" class="form-select" required>
                            @foreach($equipos as $equipo)
                            <option value="{{ $equipo->id }}"
                                {{ $partido->equipo_visitante_id == $equipo->id ? 'selected' : '' }}>
                                {{ $equipo->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Goles Local -->
                    <div class="col-md-4 mb-3">
                        <label for="editarGolesLocal" class="form-label">Goles Local</label>
                        <input type="number" name="goles_local" id="editarGolesLocal" class="form-control"
                            value="{{ $partido->goles_local ?? '' }}" min="0" required>
                    </div>

                    <!-- Goles Visitante -->
                    <div class="col-md-4 mb-3">
                        <label for="editarGolesVisitante" class="form-label">Goles Visitante</label>
                        <input type="number" name="goles_visitante" id="editarGolesVisitante" class="form-control"
                            value="{{ $partido->goles_visitante ?? '' }}" min="0" required>
                    </div>

                    <!-- Estado -->
                    <div class="col-md-4 mb-3">
                        <label for="editarEstado" class="form-label">Estado</label>
                        <select name="estado" id="editarEstado" class="form-select">
                            <option value="programado" {{ $partido->estado == 'programado' ? 'selected' : '' }}>Programado</option>
                            <option value="jugado" {{ $partido->estado == 'jugado' ? 'selected' : '' }}>Jugado</option>
                            <option value="suspendido" {{ $partido->estado == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                            <option value="cancelado" {{ $partido->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>

                    <!-- Fecha -->
                    <div class="col-md-6 mb-3">
                        <label for="editarFecha" class="form-label">Fecha</label>
                        <input type="date" name="fecha_partido" id="editarFecha" class="form-control"
                            value="{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('Y-m-d') }}">
                    </div>

                    <!-- Hora -->
                    <div class="col-md-6 mb-3">
                        <label for="editarHora" class="form-label">Hora</label>
                        <input type="time" name="hora_partido" id="editarHora" class="form-control"
                            value="{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('H:i') }}">
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Gestionar Eventos -->
<div class="modal fade" id="eventosPartidoModal" tabindex="-1" aria-labelledby="eventosPartidoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ url('/admin/partidos/eventos/guardar') }}" class="modal-content">
            @csrf
            <input type="hidden" name="partido_id" id="eventosPartidoId">
            <input type="hidden" name="eventos_json" id="eventos_json">

            <div class="modal-header">
                <h5 class="modal-title" id="eventosPartidoModalLabel">Gestionar Eventos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Aquí el mismo bloque de añadir evento que ya usas -->
                <!-- Tipo, minuto, equipo, jugador, lista -->
                <!-- REUTILIZA tu bloque JS actual aquí -->
                <p class="text-muted">Aquí va tu selector de tipo, minuto, equipo, jugador...</p>
                <!-- Reusa tu bloque de inputs + botón añadir + lista de eventos + input hidden -->
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Guardar Eventos</button>
            </div>
        </form>
    </div>
</div>

@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const botonesEliminarPartido = document.querySelectorAll('.btn-eliminar-partido');
        botonesEliminarPartido.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar este partido?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    });
</script>
@endpush