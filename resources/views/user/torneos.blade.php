@extends('user.layouts.app')

@section('title', 'Torneos Disponibles')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Torneos Activos</h1>
    <!-- Mensajes -->
    @if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
        {{ $error }}<br>
        @endforeach
    </div>
    @endif
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="row g-4">
        @foreach($torneos as $torneo)
        <div class="col-md-3 mb-4">
            <div class="card text-white shadow"
                style="background: url('{{ asset($torneo->logo) }}') ;
                background-size: contain;
                background-repeat: no-repeat;
                background-position: center;
                height: 250px;
                cursor: pointer;
                border: 3px solid #000;
                border-radius: 1rem;
                overflow: hidden;"
                data-bs-toggle="modal"
                data-bs-target="#modalLiguilla"
                data-nombre="{{ $torneo->nombre }}"
                data-id="{{ $torneo->id }}"
                data-descripcion="{{ $torneo->descripcion }}"
                data-fecha-inicio="{{ $torneo->fecha_inicio }}"
                data-fecha-fin="{{ $torneo->fecha_fin }}"
                data-num-equipos="{{ count($torneo->equipos) }}"
                data-jug-equipo="{{ $torneo->jugadores_por_equipo  }}"
                data-usa-posiciones="{{ $torneo->usa_posiciones }}">
                @if(is_null($torneo->logo))
                <div class="card-body d-flex flex-column justify-content-start align-items-center bg-dark bg-opacity-50 p-3 h-100">
                    <h2 class="card-title mb-2 text-center mt-5">{{ $torneo->nombre }}</h4>
                        <p class="card-text text-center">{{ $torneo->descripcion }}</p>
                </div>
                @endif
            </div>
        </div>
        @endforeach


        <!--Modal único -->
        <div class="modal fade" id="modalLiguilla" tabindex="-1" aria-labelledby="modalLiguillaLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ url('/user/liguillas/crear') }}">
                        @csrf
                        <input type="hidden" name="torneo_id" id="modal_torneo_id">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title" id="modalLiguillaLabel">Configurar Liguilla</h5>

                            <!-- Botón info -->
                            <button class="btn btn-sm btn-clear ps-2" type="button" data-bs-toggle="collapse" data-bs-target="#torneoInfo" aria-expanded="false" aria-controls="torneoInfo">
                                <i class="bi bi-info-circle"></i>
                            </button>

                            <!-- Botón cerrar modal -->
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="collapse mb-3" id="torneoInfo">
                                <div class="card card-body bg-light text-dark">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="card-title mb-3" style="text-decoration: underline;">
                                            Información
                                        </h6>
                                        <button class="btn btn-sm btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target="#torneoInfo" aria-expanded="true" aria-controls="torneoInfo">
                                            <i class="bi bi-chevron-up" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                    <p><strong>Nombre del Torneo:</strong> <span id="infoTorneoNombre"></span></p>
                                    <p><strong>Descripción:</strong> <span id="infoTorneoDescripcion"></span></p>
                                    <p><strong>Fecha inicio:</strong> <span id="infoTorneoFechaInicio"></span></p>
                                    <p><strong>Fecha fin:</strong> <span id="infoTorneoFechaFin"></span></p>
                                    <p><strong>Número de equipos:</strong> <span id="infoTorneoNumEquipos"></span></p>
                                    <p><strong>Número de jugadores por alineación:</strong> <span id="infoTorneoJugPorEquipo"></span></p>
                                    <p><strong>Usa posiciones:</strong> <span id="infoTorneoUsaPosiciones"></span></p>
                                </div>
                            </div>
                            <p id="modalLiguillaDescripcion" class="mb-3 text-muted"></p>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="num_max_part" class="form-label">Número máximo de particiantes</label>
                                <input type="number" name="num_max_part" id="num_max_part" class="form-control" min="0" value="10" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Crear Liguilla</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    var modalLiguilla = document.getElementById('modalLiguilla');

    modalLiguilla.addEventListener('show.bs.modal', function(event) {
        var trigger = event.relatedTarget;

        // Obtener datos del torneo desde data-attributes del botón o card
        var torneoId = trigger.getAttribute('data-id');
        var torneoNombre = trigger.getAttribute('data-nombre');
        var torneoDescripcion = trigger.getAttribute('data-descripcion') || '';
        var torneoFechaInicio = trigger.getAttribute('data-fecha-inicio') || '';
        var torneoFechaFin = trigger.getAttribute('data-fecha-fin') || '';
        var torneoNumEquipos = trigger.getAttribute('data-num-equipos') || '';
        var torneoJugPorEquipo = trigger.getAttribute('data-jug-equipo') || '';
        var torneoUsaPosiciones = trigger.getAttribute('data-usa-posiciones') || '';

        // Rellenar los campos del formulario
        var inputTorneoId = modalLiguilla.querySelector('#modal_torneo_id');
        var inputNombre = modalLiguilla.querySelector('#nombre');
        var tituloModal = modalLiguilla.querySelector('.modal-title');

        inputTorneoId.value = torneoId;
        inputNombre.value = '';
        tituloModal.textContent = 'Crear Liguilla de ' + torneoNombre;

        // Rellenar la info del colapsable
        modalLiguilla.querySelector('#infoTorneoNombre').textContent = torneoNombre;
        modalLiguilla.querySelector('#infoTorneoDescripcion').textContent = torneoDescripcion;
        modalLiguilla.querySelector('#infoTorneoFechaInicio').textContent = torneoFechaInicio;
        modalLiguilla.querySelector('#infoTorneoFechaFin').textContent = torneoFechaFin;
        modalLiguilla.querySelector('#infoTorneoNumEquipos').textContent = torneoNumEquipos;
        modalLiguilla.querySelector('#infoTorneoJugPorEquipo').textContent = torneoJugPorEquipo;
        modalLiguilla.querySelector('#infoTorneoUsaPosiciones').textContent = torneoUsaPosiciones;
    });
</script>
@endpush