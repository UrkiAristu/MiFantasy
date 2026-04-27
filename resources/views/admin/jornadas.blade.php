@extends('admin.layouts.app')

@section('title', 'Jornadas del Torneo')

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

    <!-- Errores -->
    @if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
        {{ $error }}<br>
        @endforeach
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- Selector de vista (toggle) a la izquierda -->
        <div class="d-flex align-items-center gap-2">
            <button id="btnVistaTabs" type="button" class="btn btn-primary" title="Vista pestañas" aria-label="Cambiar a vista de pestañas">
                <i class="bi bi-card-list fs-5"></i>
            </button>
            <button id="btnVistaCards" type="button" class="btn btn-outline-primary" title="Vista cards" aria-label="Cambiar a vista de cuadrícula">
                <i class="bi bi-grid-3x3-gap fs-5"></i>
            </button>
        </div>

        <!-- Botón Crear Jornada a la derecha -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearJornada">
            Crear Jornada
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Mostrar jornadas -->
    <!-- VISTA PESTAÑAS -->
    <div id="vistaTabs">
        <ul class="nav nav-tabs" id="jornadasTab" role="tablist">
            @foreach($torneo->jornadas as $index => $jornada)
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link @if($index == 0) active @endif"
                    id="tab-jornada-{{ $jornada->id }}"
                    data-bs-toggle="tab"
                    data-bs-target="#jornada-{{ $jornada->id }}"
                    type="button"
                    role="tab"
                    aria-controls="jornada-{{ $jornada->id }}"
                    aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                    {{ $jornada->nombre }}
                </button>
            </li>
            @endforeach
        </ul>
        <div class="tab-content mt-3" id="jornadasTabContent">
            @foreach($torneo->jornadas as $index => $jornada)
            <div
                class="card tab-pane fade @if($index == 0) show active @endif"
                id="jornada-{{ $jornada->id }}"
                role="tabpanel"
                aria-labelledby="tab-jornada-{{ $jornada->id }}">

                <div class="card-header d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h5>{{ $jornada->nombre }}</h5>
                        <small class="text-muted">
                            {{ $jornada->fecha_inicio ? \Carbon\Carbon::parse($jornada->fecha_inicio)->format('d/m/Y') : '-' }} -
                            {{ $jornada->fecha_fin ? \Carbon\Carbon::parse($jornada->fecha_fin)->format('d/m/Y') : '-' }}
                            . Cierre alineaciones:
                            @if($jornada->fecha_cierre_alineaciones)
                            {{ $jornada->fecha_cierre_alineaciones->format('d/m/Y H:i') }}
                            @else
                            -
                            @endif
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <button
                            class="btn btn-success btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalCrearPartido"
                            data-jornada-id="{{ $jornada->id }}"
                            data-jornada-nombre="{{ $jornada->nombre }}">
                            Añadir Partido
                        </button>
                        <button class="btn btn-warning btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditarJornada"
                            data-id="{{ $jornada->id }}"
                            data-nombre="{{ $jornada->nombre }}"
                            data-fecha-inicio="{{ $jornada->fecha_inicio }}"
                            data-fecha-fin="{{ $jornada->fecha_fin }}"
                            data-fecha-cierre-alineaciones="{{ $jornada->fecha_cierre_alineaciones }}">
                            Editar
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($jornada->partidos->count())
                    <table class="table table-striped table-hover align-middle mb-0">
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
                            @foreach($jornada->partidos as $indexP => $partido)
                            <tr>
                                <td class="text-center">{{ $indexP + 1 }}</td>
                                <td class="text-center">{{ $partido->equipoLocal->nombre }}</td>
                                <td class="text-center">{{ $partido->equipoVisitante->nombre }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('d/m/Y') }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('H:i') }}</td>
                                <td class="text-center">{{ $partido->goles_local ?? '-' }} - {{ $partido->goles_visitante ?? '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ url('/admin/partidos/'.$partido->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <button type="button" class="btn btn-sm btn-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#resultadoModal"
                                        data-partido-id="{{ $partido->id }}"
                                        data-local="{{ $partido->equipoLocal->nombre }}"
                                        data-visitante="{{ $partido->equipoVisitante->nombre }}"
                                        data-goles-local="{{ $partido->goles_local }}"
                                        data-goles-visitante="{{ $partido->goles_visitante }}">
                                        <i class="bi bi-pencil-square"></i> Resultado
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="p-3">No hay partidos programados en esta jornada.</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <!-- VISTA CARDS -->
    <div id="vistaCards" style="display:none;">
        @forelse ($torneo->jornadas as $jornada)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5>{{ $jornada->nombre }}</h5>
                    <small class="text-muted">
                        {{ $jornada->fecha_inicio ? \Carbon\Carbon::parse($jornada->fecha_inicio)->format('d/m/Y') : '-' }} -
                        {{ $jornada->fecha_fin ? \Carbon\Carbon::parse($jornada->fecha_fin)->format('d/m/Y') : '-' }}
                        . Cierre alineaciones:
                        @if($jornada->fecha_cierre_alineaciones)
                        {{ $jornada->fecha_cierre_alineaciones->format('d/m/Y H:i') }}
                        @else
                        -
                        @endif
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearPartido"
                        data-jornada-id="{{ $jornada->id }}" data-jornada-nombre="{{ $jornada->nombre }}">
                        Añadir Partido
                    </button>
                    <button class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditarJornada"
                        data-id="{{ $jornada->id }}"
                        data-nombre="{{ $jornada->nombre }}"
                        data-fecha-inicio="{{ $jornada->fecha_inicio }}"
                        data-fecha-fin="{{ $jornada->fecha_fin }}"
                        data-fecha-cierre-alineaciones="{{ $jornada->fecha_cierre_alineaciones }}">
                        Editar
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                @if ($jornada->partidos->count())
                <table class="table table-striped table-hover align-middle mb-0">
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
                        @foreach($jornada->partidos as $index => $partido)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $partido->equipoLocal->nombre }}</td>
                            <td class="text-center">{{ $partido->equipoVisitante->nombre }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('d/m/Y') }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('H:i') }}</td>
                            <td class="text-center">{{ $partido->goles_local ?? '-' }} - {{ $partido->goles_visitante ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ url('/admin/partidos/'.$partido->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <button type="button" class="btn btn-sm btn-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#resultadoModal"
                                    data-partido-id="{{ $partido->id }}"
                                    data-local="{{ $partido->equipoLocal->nombre }}"
                                    data-visitante="{{ $partido->equipoVisitante->nombre }}"
                                    data-goles-local="{{ $partido->goles_local }}"
                                    data-goles-visitante="{{ $partido->goles_visitante }}">
                                    <i class="bi bi-pencil-square"></i> Resultado
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="p-3">No hay partidos programados en esta jornada.</div>
                @endif
            </div>
        </div>
        @empty
        <p class="text-muted">Este torneo aún no tiene jornadas creadas.</p>
        @endforelse
    </div>
    @if ($torneo->jornadas->count())
    <div class="mt-4">
        <div class="d-flex justify-content-between mb-3">
            <h2>Orden de jornadas en {{ $torneo->nombre }}</h2>
        </div>

        <form id="formOrdenJornadas" method="POST" action="{{ url('/admin/torneos/'.$torneo->id.'/jornadas/guardarOrdenJornadas') }}">
            @csrf
            <ul id="lista-jornadas" class="list-group mb-3">
                @foreach ($torneo->jornadas as $jornada)
                <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $jornada->id }}">
                    <span>
                        <strong>{{ $jornada->nombre }}</strong><br>
                        <small class="text-muted">
                            {{ $jornada->fecha_inicio ? \Carbon\Carbon::parse($jornada->fecha_inicio)->format('d/m/Y') : '-' }}
                            –
                            {{ $jornada->fecha_fin ? \Carbon\Carbon::parse($jornada->fecha_fin)->format('d/m/Y') : '-' }}
                            . Cierre alineaciones:
                            @if($jornada->fecha_cierre_alineaciones)
                            {{ $jornada->fecha_cierre_alineaciones->format('d/m/Y H:i') }}
                            @else
                            -
                            @endif
                        </small>
                    </span>
                    <div class="d-flex align-items-center gap-5">
                        <i class="bi bi-list fs-1 cursor-move"></i>
                        <a href="{{ url('/admin/jornadas/'.$jornada->id.'/eliminar') }}" type="button" class="btn btn-sm btn-danger btn-eliminar-jornada" title="Eliminar jornada"
                            data-url="{{ url('/admin/jornadas/'.$jornada->id.'/eliminar') }}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </li>
                @endforeach
            </ul>
            <input type="hidden" name="orden" id="ordenInput">
            <button type="submit" class="btn btn-success">Guardar orden</button>
        </form>
    </div>
    @else
    <h2>Aún no hay jornadas en {{ $torneo->nombre }}</h2>
    @endif
</div>
<!-- Modal único para crear partido -->
<div class="modal fade" id="modalCrearPartido" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="formCrearPartido" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearPartidoTitle">Nuevo Partido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Equipo Local</label>
                    <select class="form-select" id="equipo_local_id" name="equipo_local_id" required>
                        <option value="">Selecciona un equipo local</option>
                        @foreach ($torneo->equipos as $equipo)
                        <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Equipo Visitante</label>
                    <select class="form-select" id="equipo_visitante_id" name="equipo_visitante_id" required>
                        <option value="">Selecciona un equipo visitante</option>
                        @foreach ($torneo->equipos as $equipo)
                        <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Fecha </label>
                    <input type="date" class="form-control" name="fecha_partido" required
                        min="{{ \Carbon\Carbon::parse($torneo->fecha_inicio)->format('Y-m-d') }}"
                        max="{{ \Carbon\Carbon::parse($torneo->fecha_fin)->format('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Hora</label>
                    <input type="time" class="form-control" name="hora_partido">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Guardar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>
<!-- Modal crear jornada -->
<div class="modal fade" id="modalCrearJornada" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{  url('/admin/torneos/'.$torneo->id.'/jornadas/crear') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="torneo_id" value="{{ $torneo->id }}">
            <div class="modal-header">
                <h5 class="modal-title">Crear Jornada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre de la Jornada</label>
                    <input type="text" class="form-control" name="nombre" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Inicio</label>
                        <input type="date" class="form-control" name="fecha_inicio"
                            min="{{ \Carbon\Carbon::parse($torneo->fecha_inicio)->format('Y-m-d') }}"
                            max="{{ \Carbon\Carbon::parse($torneo->fecha_fin)->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Fin</label>
                        <input type="date" class="form-control" name="fecha_fin"
                            min="{{ \Carbon\Carbon::parse($torneo->fecha_inicio)->format('Y-m-d') }}"
                            max="{{ \Carbon\Carbon::parse($torneo->fecha_fin)->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Fecha y hora de cierre de alineaciones</label>
                    <input type="datetime-local" class="form-control" name="fecha_cierre_alineaciones"
                        min="{{ \Carbon\Carbon::parse($torneo->fecha_inicio)->subDay()->format('Y-m-d\TH:i') }}"
                        max="{{ \Carbon\Carbon::parse($torneo->fecha_fin)->endOfDay()->format('Y-m-d\TH:i') }}">
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-success">Crear</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>
<!-- Modal editar jornada -->
<div class="modal fade" id="modalEditarJornada" tabindex="-1">
    <div class="modal-dialog">
        <form action="" method="POST" id="formEditarJornada" class="modal-content">
            @csrf
            <input type="hidden" name="jornada_id" id="editarJornadaId">
            <div class="modal-header">
                <h5 class="modal-title">Editar Jornada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" name="nombre" id="editarNombre" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" name="fecha_inicio" id="editarFechaInicio"
                            min="{{ \Carbon\Carbon::parse($torneo->fecha_inicio)->format('Y-m-d') }}"
                            max="{{ \Carbon\Carbon::parse($torneo->fecha_fin)->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control" name="fecha_fin" id="editarFechaFin"
                            min="{{ \Carbon\Carbon::parse($torneo->fecha_inicio)->format('Y-m-d') }}"
                            max="{{ \Carbon\Carbon::parse($torneo->fecha_fin)->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Fecha y hora de cierre de alineaciones</label>
                    <input type="datetime-local" class="form-control" name="fecha_cierre_alineaciones" id="editarFechaCierre"
                        min="{{ \Carbon\Carbon::parse($torneo->fecha_inicio)->subDay()->format('Y-m-d\TH:i') }}"
                        max="{{ \Carbon\Carbon::parse($torneo->fecha_fin)->endOfDay()->format('Y-m-d\TH:i') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success">Guardar</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal resultado (compartido) -->
<div class="modal fade" id="resultadoModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ url('/admin/partidos/actualizar-resultado') }}" class="modal-content">
            @csrf
            <input type="hidden" name="partido_id" id="modalPartidoId">
            <div class="modal-header">
                <h5 class="modal-title">Editar Resultado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p id="modalEquipos" class="fw-bold text-center mb-4"></p>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Goles Local</label>
                        <input type="number" class="form-control" name="goles_local" id="modalGolesLocal" min="0" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Goles Visitante</label>
                        <input type="number" class="form-control" name="goles_visitante" id="modalGolesVisitante" min="0" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-success">Guardar</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const lista = document.getElementById('lista-jornadas');

    const sortable = Sortable.create(lista, {
        animation: 150,
    });

    const form = document.getElementById('formOrdenJornadas');

    form.addEventListener('submit', function(e) {
        const orden = [];
        lista.querySelectorAll('li').forEach((li, index) => {
            orden.push({
                id: li.dataset.id,
                orden: index + 1
            });
        });

        document.getElementById('ordenInput').value = JSON.stringify(orden);
    });
</script>
<script>
    $(document).ready(function() {
        $('#equipo_local_id').select2({
            width: '100%',
            dropdownParent: $('#modalCrearPartido'),
            placeholder: 'Selecciona un equipo',
            allowClear: true,
            language: {
                noResults: function() {
                    return "No se encontraron equipos";
                }
            }
        });
        $('#equipo_visitante_id').select2({
            width: '100%',
            dropdownParent: $('#modalCrearPartido'),
            placeholder: 'Selecciona un equipo',
            allowClear: true,
            language: {
                noResults: function() {
                    return "No se encontraron equipos";
                }
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const botonesEliminar = document.querySelectorAll('.btn-eliminar-jornada');
        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar esta jornada?',
                    text: "Los partidos se eliminarán automáticamente.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });

        var resultadoModal = document.getElementById('resultadoModal');
        resultadoModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            resultadoModal.querySelector('#modalPartidoId').value = button.getAttribute('data-partido-id');
            resultadoModal.querySelector('#modalEquipos').textContent = button.getAttribute('data-local') + ' vs ' + button.getAttribute('data-visitante');
            resultadoModal.querySelector('#modalGolesLocal').value = button.getAttribute('data-goles-local') || '';
            resultadoModal.querySelector('#modalGolesVisitante').value = button.getAttribute('data-goles-visitante') || '';
        });

        const modalEditar = document.getElementById('modalEditarJornada');
        modalEditar.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('editarJornadaId').value = button.getAttribute('data-id');
            document.getElementById('editarNombre').value = button.getAttribute('data-nombre');
            document.getElementById('editarFechaInicio').value = button.getAttribute('data-fecha-inicio');
            document.getElementById('editarFechaFin').value = button.getAttribute('data-fecha-fin');
            document.getElementById('editarFechaCierre').value = button.getAttribute('data-fecha-cierre-alineaciones');
            const form = document.getElementById('formEditarJornada');
            form.action = `/admin/jornadas/${button.getAttribute('data-id')}/editar`;
        });

        const btnTabs = document.getElementById('btnVistaTabs');
        const btnCards = document.getElementById('btnVistaCards');
        const vistaTabs = document.getElementById('vistaTabs');
        const vistaCards = document.getElementById('vistaCards');

        function activarVista(vista) {
            if (vista === 'tabs') {
                vistaTabs.style.display = '';
                vistaCards.style.display = 'none';
                btnTabs.classList.replace('btn-outline-primary', 'btn-primary');
                btnCards.classList.replace('btn-primary', 'btn-outline-primary');
            } else {
                vistaTabs.style.display = 'none';
                vistaCards.style.display = '';
                btnTabs.classList.replace('btn-primary', 'btn-outline-primary');
                btnCards.classList.replace('btn-outline-primary', 'btn-primary');
            }
        }

        btnTabs.addEventListener('click', () => activarVista('tabs'));
        btnCards.addEventListener('click', () => activarVista('cards'));

        activarVista('tabs'); // vista por defecto
    });
</script>
<script>
    const modalCrearPartido = document.getElementById('modalCrearPartido');
    modalCrearPartido.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const nombreJornada = button.getAttribute('data-jornada-nombre');
        const jornadaId = button.getAttribute('data-jornada-id');
        const modalTitle = modalCrearPartido.querySelector('.modal-title');
        modalTitle.textContent = 'Nuevo Partido - ' + nombreJornada;
        const form = document.getElementById('formCrearPartido');
        form.action = `/admin/jornadas/${jornadaId}/partidos/crear`;
    });
</script>
@endpush