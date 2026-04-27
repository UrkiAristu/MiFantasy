@extends('user.layouts.app')

@section('title', 'Liguilla: ' . $liguilla->nombre)

@section('content')
<div class="container py-4 mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">{{ $liguilla->nombre }}</h1>
            <small class="text-muted">Torneo: {{ $liguilla->torneo->nombre ?? '-' }}</small>
        </div>
        <div>
            <span class="badge bg-primary">Código: {{ $liguilla->codigo_unico }}</span>
        </div>
    </div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-3" id="liguillaTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="alineacion-tab" data-bs-toggle="tab" data-bs-target="#alineacion" type="button" role="tab">Alineación</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="plantilla-tab" data-bs-toggle="tab" data-bs-target="#plantilla" type="button" role="tab">Plantilla</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="clasificacion-tab" data-bs-toggle="tab" data-bs-target="#clasificacion" type="button" role="tab">Clasificación</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="jornadas-tab" data-bs-toggle="tab" data-bs-target="#jornadas" type="button" role="tab">Mis Jornadas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="resultados-tab" data-bs-toggle="tab" data-bs-target="#resultados" type="button" role="tab">Resultados</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="participantes-tab" data-bs-toggle="tab" data-bs-target="#participantes" type="button" role="tab">Participantes</button>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Alineacion -->
        <div class="tab-pane fade show active" id="alineacion" role="tabpanel" aria-labelledby="alineacion-tab">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Alineación actual</h5>
                    <p class="text-muted"> Selecciona {{ $liguilla->torneo->jugadores_por_equipo }} jugadores de tu plantilla.
                    </p>

                    @php
                    $jugadoresBase = isset($jugadoresBase)
                    ? ($jugadoresBase instanceof \Illuminate\Support\Collection ? $jugadoresBase->values() : collect($jugadoresBase)->values())
                    : collect();
                    @endphp

                    <!-- Campo de fútbol -->
                    <div class="futbol-campo mb-4 position-relative">
                        <div class="alineacion-slots d-flex flex-wrap justify-content-center gap-3">
                            @for($i = 1; $i <= $liguilla->torneo->jugadores_por_equipo; $i++)
                                @php
                                $jug = $jugadoresBase->get($i - 1); // índice 0-based
                                @endphp
                                <div class="slot card text-center d-flex align-items-center justify-content-center {{ $jug ? 'ocupado' : 'vacio' }}"
                                    data-slot="{{ $i }}">
                                    <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                                        @if($jug)
                                        <img src="{{ $jug->foto ? asset($jug->foto) : asset('assets/media/images/default-player.png') }}"
                                            alt="{{ $jug->nombre }} {{ $jug->apellido1 }}"
                                            width="50"
                                            class="rounded-circle mb-1">
                                        <small class="text-white">
                                            {{ $jug->nombre }} {{ $jug->apellido1 }}
                                        </small>
                                        @else
                                        <i class="bi bi-plus-circle-fill text-white fs-3 slot-plus" style="cursor: pointer;"></i>
                                        <small class="text-white mt-1">Vacío</small>
                                        @endif
                                    </div>
                                </div>
                                @endfor
                        </div>
                    </div>

                    <!-- Formulario alineación base -->
                    <form id="formAlineacion" method="POST"
                        action="{{ url('/user/liguillas/'.$liguilla->id.'/alineacion/guardar') }}"
                        class="mt-4">
                        @csrf

                        <div id="alineacionInputs">
                            @foreach($jugadoresBase as $index => $jug)
                            <input type="hidden"
                                name="jugadores[]"
                                data-slot="{{ $index + 1 }}"
                                value="{{ $jug->id }}">
                            @endforeach
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" {{ $bloqueada ? 'disabled' : '' }}>
                                Guardar alineación
                            </button>
                            <button type="button" class="btn btn-secondary">Cancelar</button>
                        </div>
                    </form>

                    @if($bloqueada)
                    <div class="alert alert-warning mt-3">
                        ⚠️ La jornada ya ha comenzado, no puedes modificar la alineación.
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Modal Plantilla -->
        <div class="modal fade" id="modalSeleccionJugador" tabindex="-1" aria-labelledby="modalSeleccionJugadorLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalSeleccionJugadorLabel">Selecciona jugador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row row-cols-2 row-cols-md-4 g-3">
                            @foreach($miPlantilla as $jugador)
                            <div class="col">
                                <div class="card jugador-card selectable" data-jugador-id="{{ $jugador->id }}">
                                    <div class="card-body text-center p-2">
                                        <div class="jugador-avatar mb-2">
                                            <img src="{{ $jugador->equipoEnTorneo($liguilla->torneo_id)->logo ? asset($jugador->equipoEnTorneo($liguilla->torneo_id)->logo) : asset('assets/media/images/default-team.png') }}" alt="logo equipo">
                                        </div>
                                        <img src="{{ $jugador->foto ? asset($jugador->foto) : asset('assets/media/images/default-player.png') }}"
                                            alt="{{ $jugador->nombre }} {{ $jugador->apellido1 }}"
                                            width="60" class="rounded-circle mb-2">
                                        <h6 class="mb-0">{{ $jugador->nombre }} {{ $jugador->apellido1 }}</h6>
                                        <small class="text-muted">{{ $jugador->posicion ?? 'Jugador' }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnVaciarSlot" class="btn btn-outline-danger">Vaciar posición</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plantilla -->
        <div class="tab-pane fade" id="plantilla" role="tabpanel" aria-labelledby="plantilla-tab">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Tu Plantilla <small>({{ $miPlantilla->count() }} jugadores)</small></h5>
                    <div class="row row-cols-2 row-cols-md-4 g-3">
                        @foreach($miPlantilla as $jugador)
                        <div class="col">
                            <div class="card jugador-card" data-jugador-id="{{ $jugador->id }}">
                                <div class="card-body text-center p-2">
                                    <div class="jugador-avatar mb-2">
                                        <img src="{{ $jugador->equipoEnTorneo($liguilla->torneo_id)->logo ? asset($jugador->equipoEnTorneo($liguilla->torneo_id)->logo) : asset('assets/media/images/default-team.png') }}"
                                            alt="{{ $jugador->equipoEnTorneo($liguilla->torneo_id)->nombre }}">
                                    </div>
                                    <img src="{{ $jugador->foto ? asset($jugador->foto) : asset('assets/media/images/default-player.png') }}"
                                        alt="{{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}" width="80" class="rounded-circle mb-2">
                                    <h4 class="mb-0">{{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}</h4>
                                    <h6 class="text-muted mb-0">{{ $jugador->equipoEnTorneo($liguilla->torneo_id)->nombre }}</h6>
                                    <small class="text-muted">{{ $jugador->posicion ?? 'Jugador' }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Jugador -->
        <div class="modal fade" id="modalJugador" tabindex="-1" aria-labelledby="modalJugadorLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalJugadorLabel">Información del Jugador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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

        <!-- Clasificación -->
        <div class="tab-pane fade" id="clasificacion" role="tabpanel" aria-labelledby="clasificacion-tab">
            <div class="card mb-4">
                <div class="card-body">

                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            Clasificación
                            <small id="clasificacion-subtitle" class="text-muted">
                                Total liguilla
                            </small>
                        </h5>

                        {{-- Selector de tipo de clasificación --}}
                        <div class="d-flex align-items-center gap-2">
                            <label for="selectClasificacion" class="small mb-0">Ver:</label>
                            <select id="selectClasificacion"
                                class="form-select form-select-sm"
                                data-url-clasificacion="{{ route('liguillas.clasificacionAjax', $liguilla) }}">
                                <option value="global" selected>
                                    Global
                                </option>
                                @foreach($jornadas as $j)
                                <option value="{{ $j->id }}">
                                    Jornada {{ $j->orden ?? $loop->iteration }}
                                    @if($j->nombre) – {{ $j->nombre }} @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="tablaClasificacion" class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Usuario</th>
                                    <th class="text-end">Puntos</th>
                                    <th id="thAlineacion" class="text-end d-none">Alineación</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyClasificacion">
                                {{-- Clasificación inicial (global) renderizada en servidor --}}
                                @foreach($clasificacion as $u)
                                <tr>
                                    <td>{{ $u->posicion }}</td>
                                    <td>
                                        {{ $u->name ?? $u->email ?? 'Usuario' }}
                                        @if(isset($usuario) && $usuario->id === $u->id)
                                        <span class="badge bg-primary ms-1">Tú</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ $u->puntos }}</td>
                                    <td class="text-end"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        {{-- Modal Alineación --}}
        <div class="modal fade" id="modalAlineacionClasificacion" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Alineación de <span id="alineacionModalUsuario"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">

                        {{-- Campo de fútbol --}}
                        <div class="futbol-campo mb-3 position-relative">
                            <div class="alineacion-slots d-flex flex-wrap justify-content-center gap-3"
                                id="alineacionModalSlots">
                                @for($i = 1; $i <= $liguilla->torneo->jugadores_por_equipo; $i++)
                                    <div class="slot card text-center d-flex align-items-center justify-content-center ocupado"
                                        data-slot="{{ $i }}"
                                        data-readonly="1">
                                        <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                                            <small class="text-white mt-1">Vacío</small>
                                        </div>
                                    </div>
                                    @endfor
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <small class="text-primary">
                                Total puntos jornada: <span id="alineacionModalTotal">0</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Mis Jornadas -->
        <div class="tab-pane fade" id="jornadas" role="tabpanel" aria-labelledby="jornadas-tab">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Mis jornadas</h5>
                    <p class="text-muted">
                        Alineaciones de jornadas pasadas y puntos obtenidos.
                    </p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="list-group" id="listaMisJornadas">
                                @php
                                $misJornadas = $misAlineaciones->pluck('jornada')->filter()->unique('id')->sortBy('orden');
                                @endphp

                                @forelse($misJornadas as $j)
                                <button type="button"
                                    class="list-group-item list-group-item-action mis-jornada-link"
                                    data-jornada-id="{{ $j->id }}">
                                    Jornada {{ $j->orden }} - {{ $j->nombre }}
                                </button>
                                @empty
                                <div class="text-muted">Todavía no tienes alineaciones congeladas.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div id="panelMisJornadas">
                                <h6 class="text-muted m-3">Selecciona una jornada para ver tu alineación y puntos.</h6>

                                <div class="futbol-campo mb-3 position-relative d-none" id="campoMisJornadas">
                                    <div class="alineacion-slots d-flex flex-wrap justify-content-center gap-3" id="misJornadasSlots">
                                        @for($i = 1; $i <= $liguilla->torneo->jugadores_por_equipo; $i++)
                                            <div class="slot card text-center d-flex align-items-center justify-content-center vacio"
                                                data-slot="{{ $i }}"
                                                data-readonly="1">
                                                <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                                                    <small class="text-white mt-1">Vacío</small>
                                                </div>
                                            </div>
                                            @endfor
                                    </div>
                                </div>

                                <div id="misJornadasPuntos" class="d-none">
                                    <h5>Total puntos: <span id="totalPuntosJornada">0</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Resultados -->
        <div class="tab-pane fade" id="resultados" role="tabpanel" aria-labelledby="resultados-tab">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Resultados por jornada</h5>

                    @if($jornadas->count())
                    {{-- Pestañas internas por jornada --}}
                    <ul class="nav nav-pills mb-3" id="resultadosJornadasTabs" role="tablist">
                        @foreach($jornadas as $j)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($loop->first) active @endif"
                                id="resultados-jornada-tab-{{ $j->id }}"
                                data-bs-toggle="tab"
                                data-bs-target="#resultados-jornada-{{ $j->id }}"
                                type="button"
                                role="tab">
                                J{{ $j->orden }}
                                @if($j->nombre)
                                <small class="d-block text-muted" style="font-size: 0.7rem;">
                                    {{ $j->nombre }}
                                </small>
                                @endif
                            </button>
                        </li>
                        @endforeach
                    </ul>

                    <div class="tab-content" id="resultadosJornadasContent">
                        @foreach($jornadas as $j)
                        <div class="tab-pane fade @if($loop->first) show active @endif"
                            id="resultados-jornada-{{ $j->id }}"
                            role="tabpanel"
                            aria-labelledby="resultados-jornada-tab-{{ $j->id }}">

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong>Jornada {{ $j->orden }} - {{ $j->nombre }}</strong><br>
                                    <small class="text-muted">
                                        {{ $j->fecha_inicio ? \Carbon\Carbon::parse($j->fecha_inicio)->format('d/m/Y') : '-' }}
                                        @if($j->fecha_fin)
                                        – {{ \Carbon\Carbon::parse($j->fecha_fin)->format('d/m/Y') }}
                                        @endif
                                    </small>
                                </div>
                            </div>

                            @if($j->partidos->count())
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Local</th>
                                            <th class="text-center">Marcador</th>
                                            <th class="text-center">Visitante</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($j->partidos as $p)
                                        <tr>
                                            <td class="text-center">{{ $p->equipoLocal->nombre ?? '—' }}</td>
                                            <td class="text-center">
                                                @if(!is_null($p->goles_local) && !is_null($p->goles_visitante))
                                                <strong>{{ $p->goles_local }} - {{ $p->goles_visitante }}</strong>
                                                @else
                                                <span class="text-muted">–</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $p->equipoVisitante->nombre ?? '—' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted">No hay partidos registrados para esta jornada.</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted">No hay jornadas definidas aún.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Participantes -->
        <div class="tab-pane fade" id="participantes" role="tabpanel" aria-labelledby="participantes-tab">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Participantes de la liguilla</h5>
                    <div class="list-group list-group-flush">
                        @foreach($liguilla->plantillas as $plantilla)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center me-3"
                                    style="width: 36px; height: 36px;">
                                    {{ strtoupper(substr($plantilla->usuario->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <strong>{{ $plantilla->usuario->name ?? 'Usuario' }}</strong><br>
                                    <small class="text-muted">
                                        Plantilla:
                                        <span class="badge bg-secondary text-primary">
                                            {{ $plantilla->jugadores->count() }} jugadores
                                        </span>
                                    </small>
                                </div>
                            </div>
                            <div class="text-end">
                                <a href="{{ url("/user/liguillas/{$liguilla->id}/usuario/{$plantilla->usuario->id}/plantilla") }}"
                                    class="btn btn-sm btn-primary">
                                    Ver plantilla
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    /* Pequeños ajustes de estilo */
    .card .card-body {
        padding: 1.25rem;
    }

    .alineacion-check {
        transform: scale(1.05);
    }

    .jornada-link {
        cursor: pointer;
    }

    .futbol-campo {
        background: linear-gradient(#2e7d32 50%, #1b5e20 50%);
        background-size: 100% 40px;
        border: 2px solid #fff;
        border-radius: 10px;
        padding: 20px;
        color: white;
        min-height: 250px;
    }

    .slot {
        width: 100px;
        height: 120px;
        background: rgba(255, 255, 255, 0.1);
        border: 2px dashed #ffffff;
        border-radius: 8px;
    }

    .slot.vacio:hover,
    .slot.ocupado:hover {
        border-color: #0d6efd;
        cursor: pointer;
    }

    .slot.ocupado {
        border: 2px solid #ffffff;
    }

    .slot.vacio {
        border: 2px dashed #ffffff;
    }

    .jugador-card {
        cursor: pointer;
        transition: transform .2s;
    }

    .jugador-card:hover {
        transform: scale(1.05);
    }

    .jugador-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        line-height: 40px;
    }

    .jugador-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        /* opcional, redondeado */
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: #f0f0f0;
        /* color de fondo genérico */
        font-size: 24px;
        /* tamaño del ⚽ */
    }

    .jugador-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* para que el logo no se deforme */
    }

    .loader-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        /* oscurece el área */
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        /* por encima de los slots */
    }
</style>
@endpush

@push('scripts')
<script>
    let slotSeleccionado = null;

    // Abrir modal al pulsar el slot
    document.querySelectorAll('.slot').forEach(slot => {
        slot.addEventListener('click', function() {
            slotSeleccionado = this;
            if (slot.dataset.readonly === '1') {
                return;
            }
            actualizarJugadoresDisponibles(); // refrescar para ocultar ocupados
            const modal = new bootstrap.Modal(document.getElementById('modalSeleccionJugador'));
            modal.show();
        });
    });

    // Seleccionar jugador en alineación modal
    document.querySelectorAll('.jugador-card.selectable').forEach(jugador => {
        jugador.addEventListener('click', function() {
            if (!slotSeleccionado) return;

            const jugadorId = this.dataset.jugadorId;
            const nombre = this.querySelector('h6').textContent;
            const foto = this.querySelector('img.rounded-circle').src;

            // Evitar duplicados: comprobar si ya está en inputs
            if (document.querySelector(`#alineacionInputs input[value="${jugadorId}"]`)) {
                alert("Ese jugador ya está en la alineación.");
                return;
            }

            // Rellenar el slot
            const body = slotSeleccionado.querySelector('.card-body');
            body.innerHTML = '';
            const img = document.createElement('img');
            img.src = foto;
            img.width = 50;
            img.classList.add('rounded-circle', 'mb-1');
            body.appendChild(img);
            // Marcar el slot como editado
            slotSeleccionado.classList.remove('ocupado');
            slotSeleccionado.classList.add('vacio');


            const nombreEl = document.createElement('small');
            nombreEl.textContent = nombre;
            nombreEl.classList.add('text-white');
            body.appendChild(nombreEl);

            // Añadir / actualizar input hidden
            let slotNumber = slotSeleccionado.dataset.slot;
            let formInput = document.querySelector(`#alineacionInputs input[data-slot="${slotNumber}"]`);
            if (!formInput) {
                formInput = document.createElement('input');
                formInput.type = 'hidden';
                formInput.name = 'jugadores[]';
                formInput.dataset.slot = slotNumber;
                document.getElementById('alineacionInputs').appendChild(formInput);
            }
            formInput.value = jugadorId;

            bootstrap.Modal.getInstance(document.getElementById('modalSeleccionJugador')).hide();
        });
    });

    //Seleccionar jugador en plantilla modal
    document.querySelectorAll('#plantilla .jugador-card').forEach(card => {
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

    // Botón vaciar slot
    document.getElementById('btnVaciarSlot').addEventListener('click', function() {
        if (!slotSeleccionado) return;

        const body = slotSeleccionado.querySelector('.card-body');
        body.innerHTML = '<i class="bi bi-plus-circle-fill text-white fs-3 slot-plus" style="cursor: pointer;"></i><small class="text-white mt-1">Vacío</small>';

        let slotNumber = slotSeleccionado.dataset.slot;
        const input = document.querySelector(`#alineacionInputs input[data-slot="${slotNumber}"]`);
        if (input) input.remove();

        bootstrap.Modal.getInstance(document.getElementById('modalSeleccionJugador')).hide();
        // Marcar el slot como vacío
        slotSeleccionado.classList.add('vacio');
        slotSeleccionado.classList.remove('ocupado');
    });

    //Submit
    document.getElementById('formAlineacion').addEventListener('submit', function(e) {
        e.preventDefault(); // evitar recarga

        let form = e.target;
        let formData = new FormData(form);
        fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json' // para que Laravel devuelva JSON
                },
                body: formData
            })
            .then(async response => {
                if (!response.ok) {
                    // error de validación u otro
                    let errorData = await response.json();
                    throw errorData;
                }
                return response.json();
            })
            .then(data => {
                // 1) Marcar visualmente los slots según lo que se acaba de guardar
                document.querySelectorAll('.slot').forEach(slot => {
                    const slotNumber = slot.dataset.slot;
                    const hidden = document.querySelector(`#alineacionInputs input[data-slot="${slotNumber}"]`);

                    if (hidden) {
                        // Este slot tiene un jugador en la alineación guardada
                        slot.classList.add('ocupado');
                        slot.classList.remove('vacio');
                    } else {
                        // Este slot se ha quedado vacío en la alineación
                        slot.classList.add('vacio');
                        slot.classList.remove('ocupado');
                    }
                });
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: data.message || 'La alineación se guardó correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                let message = error.message || 'Ocurrió un error al guardar la alineación';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            });
    });

    // Ocultar jugadores ya ocupados
    function actualizarJugadoresDisponibles() {
        const usados = Array.from(document.querySelectorAll('#alineacionInputs input')).map(i => i.value);
        document.querySelectorAll('.jugador-card.selectable').forEach(card => {
            if (usados.includes(card.dataset.jugadorId)) {
                card.classList.add('opacity-50', 'pe-none'); // desactiva
            } else {
                card.classList.remove('opacity-50', 'pe-none');
            }
        });
    }

    // Inicializar al cargar
    document.addEventListener('DOMContentLoaded', function() {
        actualizarJugadoresDisponibles();

        const liguillaId = "{{ $liguilla->id }}";

        // Click en una jornada de "Mis Jornadas"
        document.querySelectorAll('.mis-jornada-link').forEach(btn => {
            btn.addEventListener('click', function() {
                const jornadaId = this.dataset.jornadaId;
                cargarAlineacionJornada(liguillaId, jornadaId);

                // marcar activo visualmente
                document.querySelectorAll('.mis-jornada-link').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });

    function cargarAlineacionJornada(liguillaId, jornadaId) {
        const campo = document.getElementById('campoMisJornadas');
        const slotsContainer = document.getElementById('misJornadasSlots');
        const panelPuntos = document.getElementById('misJornadasPuntos');
        const totalPuntosEl = document.getElementById('totalPuntosJornada');

        fetch(`/user/liguillas/${liguillaId}/alineacion/${jornadaId}`)
            .then(res => res.json())
            .then(data => {
                campo.classList.remove('d-none');
                panelPuntos.classList.remove('d-none');

                // Reset slots
                const slots = slotsContainer.querySelectorAll('.slot');
                slots.forEach(slot => {
                    const body = slot.querySelector('.card-body');
                    body.innerHTML = '<small class="text-white mt-1">Vacío</small>';
                    slot.classList.add('vacio');
                    slot.classList.remove('ocupado');
                });

                if (data.status !== 'ok' || !data.jugadores || data.jugadores.length === 0) {
                    totalPuntosEl.textContent = 0;
                    return;
                }

                // Rellenar slots en orden de array
                data.jugadores.forEach((jug, index) => {
                    const slot = slotsContainer.querySelector(`.slot[data-slot="${index + 1}"]`);
                    if (!slot) return;

                    const body = slot.querySelector('.card-body');
                    body.innerHTML = '';
                    body.classList.add('position-relative');

                    const badge = document.createElement('span');
                    badge.textContent = jug.puntos ?? 0;
                    badge.classList.add(
                        'badge', 'bg-warning', 'text-dark',
                        'position-absolute', 'top-0', 'end-0', 'me-1', 'mt-1'
                    );
                    body.appendChild(badge);

                    const img = document.createElement('img');
                    img.src = jug.foto || '/assets/media/images/default-player.png';
                    img.width = 50;
                    img.height = 50;
                    img.classList.add('rounded-circle', 'mb-1');
                    body.appendChild(img);

                    const nombreEl = document.createElement('small');
                    nombreEl.textContent = `${jug.nombre} ${jug.apellido1}`;
                    nombreEl.classList.add('text-white', 'text-center');
                    body.appendChild(nombreEl);

                    slot.classList.remove('vacio');
                    slot.classList.add('ocupado');
                });

                totalPuntosEl.textContent = data.total_puntos ?? 0;
            })
            .catch(err => {
                console.error(err);
                alert('No se pudo cargar la alineación de esa jornada.');
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const selectClasificacion = document.getElementById('selectClasificacion');
        const tbodyClasificacion = document.getElementById('tbodyClasificacion');
        const subtitleEl = document.getElementById('clasificacion-subtitle');
        const thAlineacion = document.getElementById('thAlineacion');
        const currentUserId = "{{Auth::id() ?? 'null'}}";

        if (selectClasificacion && tbodyClasificacion) {
            selectClasificacion.addEventListener('change', function() {
                const url = this.dataset.urlClasificacion;
                const modo = this.value;

                // Loading
                tbodyClasificacion.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </td>
                    </tr>
                `;

                fetch(`${url}?modo_clasificacion=${encodeURIComponent(modo)}`)
                    .then(res => res.json())
                    .then(data => {
                        // Subtítulo
                        if (data.modo === 'global') {
                            subtitleEl.textContent = 'Total liguilla';
                            thAlineacion.classList.add('d-none');
                        } else {
                            const j = data.jornada || {};
                            subtitleEl.textContent = (`Jornada ${j.orden ?? ''} ${j.nombre ?? ''}`).trim();
                            thAlineacion.classList.remove('d-none');
                        }

                        tbodyClasificacion.innerHTML = '';

                        if (!data.clasificacion || data.clasificacion.length === 0) {
                            tbodyClasificacion.innerHTML = `
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        No hay datos de clasificación para esta selección.
                                    </td>
                                </tr>
                            `;
                            return;
                        }

                        data.clasificacion.forEach(u => {
                            const tr = document.createElement('tr');

                            // Posición
                            const tdPos = document.createElement('td');
                            tdPos.textContent = u.posicion ?? '-';
                            tr.appendChild(tdPos);

                            // Usuario
                            const tdUser = document.createElement('td');
                            const name = u.name || u.email || 'Usuario';
                            tdUser.textContent = name;

                            if (currentUserId && Number(currentUserId) === Number(u.id)) {
                                const badge = document.createElement('span');
                                badge.className = 'badge bg-primary ms-1';
                                badge.textContent = 'Tú';
                                tdUser.appendChild(badge);
                            }

                            tr.appendChild(tdUser);

                            // Puntos
                            const tdPts = document.createElement('td');
                            tdPts.className = 'text-end';
                            tdPts.textContent = u.puntos ?? 0;
                            tr.appendChild(tdPts);

                            // Alineación (solo si no es global)
                            const tdAli = document.createElement('td');
                            tdAli.className = 'text-end';

                            if (data.modo !== 'global' && data.jornada) {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'btn btn-sm btn-primary ver-alineacion-btn';
                                btn.dataset.userId = u.id;
                                btn.dataset.jornadaId = data.jornada.id;
                                btn.dataset.userName = name;
                                btn.textContent = 'Ver alineación';
                                tdAli.appendChild(btn);
                            }

                            tr.appendChild(tdAli);

                            tbodyClasificacion.appendChild(tr);
                        });
                    })
                    .catch(err => {
                        console.error(err);
                        tbodyClasificacion.innerHTML = `
                            <tr>
                                <td colspan="4" class="text-center text-danger">
                                    Error al cargar la clasificación.
                                </td>
                            </tr>
                        `;
                    });
            });
        }

        // Modal único para "Ver alineación"
        document.body.addEventListener('click', function(e) {
            const btn = e.target.closest('.ver-alineacion-btn');
            if (!btn) return;

            const userId = btn.dataset.userId;
            const jornadaId = btn.dataset.jornadaId;
            const userName = btn.dataset.userName;

            const modalEl = document.getElementById('modalAlineacionClasificacion');
            const modal = new bootstrap.Modal(modalEl);
            const titleUser = document.getElementById('alineacionModalUsuario');
            const totalEl = document.getElementById('alineacionModalTotal');
            const slotsWrap = document.getElementById('alineacionModalSlots');

            titleUser.textContent = userName;
            totalEl.textContent = '...';

            // Resetear todos los slots a "Vacío"
            slotsWrap.querySelectorAll('.slot').forEach(slot => {
                slot.classList.add('vacio');
                slot.innerHTML = `
                    <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                        <small class="text-white mt-1">Vacío</small>
                    </div>
                `;
            });

            // Mostrar modal ya (para que se vea el campo) mientras carga
            modal.show();

            fetch(`/user/liguillas/{{ $liguilla->id }}/alineacion-usuario/${userId}/jornada/${jornadaId}`)
                .then(res => res.json())
                .then(data => {
                    if (!data || data.status !== 'ok' || !data.jugadores || data.jugadores.length === 0) {
                        totalEl.textContent = 0;
                        return;
                    }

                    let total = 0;

                    data.jugadores.forEach((jug, index) => {
                        const slot = slotsWrap.querySelector(`[data-slot="${index + 1}"]`);
                        if (!slot) return;

                        slot.classList.remove('vacio');
                        slot.classList.add('ocupado');

                        slot.innerHTML = `
                            <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                                <span class="badge bg-warning text-dark position-absolute top-0 end-0 me-1 mt-1">
                                    ${jug.puntos ?? 0}
                                </span>
                                <img src="${jug.foto || '/assets/media/images/default-player.png'}"
                                    alt="${jug.nombre} ${jug.apellido1}"
                                    class="rounded-circle mb-1"
                                    width="40" height="40">
                                <small class="text-white">${jug.nombre} ${jug.apellido1}</small>
                            </div>
                        `;
                    });

                    totalEl.textContent = data.total_puntos ?? 0;
                })
                .catch(err => {
                    console.error(err);
                    totalEl.textContent = 0;
                });
        });
    });
</script>
@endpush