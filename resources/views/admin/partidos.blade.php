@extends('admin.layouts.app')

@section('title', 'Partidos del Torneo')

@section('content')
<div class="container mt-5">
    <div class="text-center mb-4">
        <a href="{{ url('/admin/torneos/'.$torneo->id) }}" style="text-decoration: none; color: inherit;">
            @if($torneo->logo)
            <img src="{{ asset($torneo->logo) }}" alt="Logo Torneo" class="img-fluid mb-2" style="max-height: 150px;">
            @endif
            <h1>{{ $torneo->nombre }}</h1>
        </a>
    </div>
    <!-- Mensajes -->
    @if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
        {{ $error }}<br>
        @endforeach
    </div>
    @endif
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Partidos Programados</h5>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearPartidoModal">
                    Crear Partido
                </button>
            </div>
            @if($partidos->count())
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Local</th>
                        <th class="text-center">Visitante</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Hora</th>
                        <th class="text-center">Marcador</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($partidos as $index => $partido)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">
                            <a href="{{ url('/admin/equipos/'.$partido->equipoLocal->id) }}" style="text-decoration: none; color: inherit;">
                                @if($partido->equipoLocal->logo)
                                <img src="{{ asset($partido->equipoLocal->logo) }}" alt="Logo {{ $partido->equipoLocal->nombre }}" class="img-fluid me-2" style="max-height: 40px;">
                                @endif
                                {{ $partido->equipoLocal->nombre }}
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="{{ url('/admin/equipos/'.$partido->equipoVisitante->id) }}">
                                {{ $partido->equipoVisitante->nombre }}
                                @if($partido->equipoVisitante->logo)
                                <img src="{{ asset($partido->equipoVisitante->logo) }}" alt="Logo {{ $partido->equipoVisitante->nombre }}" class="img-fluid me-2" style="max-height: 40px;">
                                @endif
                            </a>
                        </td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('d/m/Y') }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('H:i') }}</td>
                        <td class="text-center">
                            {{ $partido->goles_local ?? '-' }} - {{ $partido->goles_visitante ?? '-' }}
                        </td>
                        <td class="text-center">
                            <a href="{{ url('/admin/partidos/'.$partido->id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <button type="button" class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#resultadoModal"
                                data-partido-id="{{ $partido->id }}"
                                data-local="{{ $partido->equipoLocal->nombre }}"
                                data-visitante="{{ $partido->equipoVisitante->nombre }}"
                                data-goles-local="{{ $partido->goles_local }}"
                                data-goles-visitante="{{ $partido->goles_visitante }}">
                                <i class="bi bi-pencil-square"></i> Editar Resultado
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-muted">No hay partidos programados para este torneo aún.</p>
            @endif
        </div>
    </div>
</div>
<!-- Modal para crear partido -->
<div class="modal fade" id="crearPartidoModal" tabindex="-1" aria-labelledby="crearPartidoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ url('/admin/torneos/'.$torneo->id.'/partidos/crear') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="crearPartidoModalLabel">Crear Partido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="equipo_local_id" class="form-label">Equipo Local</label>
                    <select class="form-select" id="equipo_local_id" name="equipo_local_id" required>
                        <option value="">Seleccione un equipo</option>
                        @foreach($torneo->equipos as $equipo)
                        <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="equipo_visitante_id" class="form-label">Equipo Visitante</label>
                    <select class="form-select" id="equipo_visitante_id" name="equipo_visitante_id" required>
                        <option value="">Seleccione un equipo</option>
                        @foreach($torneo->equipos as $equipo)
                        <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input
                        type="date"
                        class="form-control"
                        id="fecha_partido"
                        name="fecha_partido"
                        required
                        min="{{ \Carbon\Carbon::parse($torneo->fecha_inicio)->format('Y-m-d') }}"
                        max="{{ \Carbon\Carbon::parse($torneo->fecha_fin)->format('Y-m-d') }}">
                </div>
                <div class="mb-3">
                    <label for="hora_partido" class="form-label">Hora</label>
                    <input type="time" class="form-control" id="hora_partido" name="hora_partido" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Crear</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>
<!-- Modal Resultado -->
<div class="modal fade" id="resultadoModal" tabindex="-1" aria-labelledby="resultadoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ url('/admin/partidos/actualizar-resultado') }}" class="modal-content">
            @csrf
            @method('POST')
            <input type="hidden" name="partido_id" id="modalPartidoId">

            <div class="modal-header">
                <h5 class="modal-title" id="resultadoModalLabel">Gestionar Resultado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <p id="modalEquipos" class="fw-bold text-center mb-4"></p>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label for="goles_local" class="form-label">Goles Local</label>
                        <input type="number" class="form-control" id="modalGolesLocal" name="goles_local" min="0" required>
                    </div>

                    <div class="col-6 mb-3">
                        <label for="goles_visitante" class="form-label">Goles Visitante</label>
                        <input type="number" class="form-control" id="modalGolesVisitante" name="goles_visitante" min="0" required>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Guardar Resultado</button>
            </div>
        </form>
    </div>
</div>

@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var resultadoModal = document.getElementById('resultadoModal');
        resultadoModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;

            var partidoId = button.getAttribute('data-partido-id');
            var local = button.getAttribute('data-local');
            var visitante = button.getAttribute('data-visitante');
            var golesLocal = button.getAttribute('data-goles-local') || '';
            var golesVisitante = button.getAttribute('data-goles-visitante') || '';

            resultadoModal.querySelector('#modalPartidoId').value = partidoId;
            resultadoModal.querySelector('#modalEquipos').textContent = local + ' vs ' + visitante;
            resultadoModal.querySelector('#modalGolesLocal').value = golesLocal;
            resultadoModal.querySelector('#modalGolesVisitante').value = golesVisitante;
        });
    });
</script>
@endpush