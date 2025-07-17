@extends('admin.layouts.app')

@section('title', 'Detalle del Equipo')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Detalle del Equipo</h1>

    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Se encontraron errores:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif


    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Información del Equipo</h5>
            <form method="POST" action="{{ url('/admin/equipos/'.$equipo->id.'/editar') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Columna izquierda: Logo Actual -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Logo Actual</label><br>
                        @if($equipo->logo)
                        <img src="{{ asset($equipo->logo) }}"
                            alt="Logo del equipo"
                            class="img-thumbnail"
                            style="max-width: 160px; height: auto;">
                        <!-- Checkbox para eliminar logo -->
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="eliminar_logo" id="eliminar_logo" value="1">
                            <label class="form-check-label" for="eliminar_logo">
                                Eliminar logo actual
                            </label>
                        </div>
                        @else
                        <p class="text-muted">Sin logo</p>
                        @endif
                    </div>

                    <!-- Columna derecha: Formulario -->
                    <div class="col-md-9">
                        <!-- Fila 1: Cambiar Logo + Nombre -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="logo" class="form-label">Cambiar Logo</label>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre del Equipo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $equipo->nombre) }}" required>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    <a href="{{ url('/admin/equipos/'.$equipo->id.'/eliminar') }}"
                        class="btn btn-danger btn-eliminar-equipo"
                        title="Eliminar el equipo"
                        data-url="{{ url('/admin/equipos/'.$equipo->id.'/eliminar') }}">
                        Eliminar
                    </a>
                    <a href="{{ url('/admin/equipos') }}" class="btn btn-secondary">Volver</a>
                </div>
            </form>
        </div>
    </div>
    <div class="row mt-5">
        <!-- Card Torneos Inscritos -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Torneos Inscritos</h5>
                        <!-- Botón para abrir modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#torneoModal">
                            Añadir Torneo
                        </button>
                    </div>

                    @if($equipo->torneos->count())
                    <table id="tablaTorneosEquipo" class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Logo</th>
                                <th class="text-center">Nombre del Torneo</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($equipo->torneos as $index => $torneo)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">
                                    @if($torneo->logo)
                                    <img src="{{ asset($torneo->logo) }}" alt="Logo" style="width:40px; height:40px; object-fit:contain;">
                                    @else
                                    <span class="text-muted">Sin logo</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $torneo->nombre }}</td>
                                <td class="text-center">
                                    <a href="{{ url('/admin/torneos/'.$torneo->id) }}" class="btn btn-info btn-sm m-1" title="Ver torneo">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <a href="{{ url('/admin/torneos/'.$torneo->id.'/equipos/'.$equipo->id.'/jugadores') }}" class="btn btn-warning btn-sm m-1" title="Ver jugadores del equipo en este torneo">
                                        <i class="bi bi-people"></i> Jugadores
                                    </a>
                                    <a href="{{ url('/admin/torneos/'.$torneo->id.'/partidos') }}" class="btn btn-sm btn-primary" title="Ver partidos">
                                        <i class="bi bi-calendar-event"></i> Partidos
                                    </a>
                                    <a href="{{ url('/admin/equipos/'.$equipo->id.'/torneos/'.$torneo->id.'/eliminar') }}"
                                        class="btn btn-danger btn-sm btn-desapuntar-equipo m-1"
                                        title="Desapuntar del torneo"
                                        data-url="{{ url('/admin/equipos/'.$equipo->id.'/torneos/'.$torneo->id.'/eliminar') }}">
                                        <i class="bi bi-x-circle"></i> Quitar
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-muted mb-0">No hay torneos inscritos todavía.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card Jugadores del Equipo -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Jugadores del Equipo</h5>
                        <!-- Botón para añadir jugador (opcional) -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#jugadorModal">
                            Añadir Jugador
                        </button>
                    </div>

                    @if($equipo->jugadores->count())
                    <table id="tablaJugadoresEquipo" class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Logo</th>
                                <th class="text-center">Nombre del Jugador</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($equipo->jugadores as $index => $jugador)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">
                                    @if($jugador->foto)
                                    <img src="{{ asset($jugador->foto) }}" alt="Foto" style="width:40px; height:40px; object-fit:contain;">
                                    @else
                                    <span class="text-muted">Sin foto</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}</td>
                                <td class="text-center">
                                    <a href="{{ url('/admin/jugadores/'.$jugador->id) }}" class="btn btn-info btn-sm" title="Ver jugador">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <a href="{{ url('/admin/equipos/'.$equipo->id.'/jugadores/'.$jugador->id.'/eliminar') }}"
                                        class="btn btn-danger btn-sm btn-expulsar-jugador"
                                        title="Expulsar del equipo"
                                        data-url="{{ url('/admin/equipos/'.$equipo->id.'/jugadores/'.$jugador->id.'/eliminar') }}">
                                        <i class="bi bi-x-circle"></i> Quitar
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-muted mb-0">No hay jugadores en este equipo.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
<!-- Modal -->
<div class="modal fade" id="torneoModal" tabindex="-1" aria-labelledby="torneoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="torneoModalLabel">Gestionar Torneos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs mb-3" id="torneoTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="seleccionarTorneo-tab" data-bs-toggle="tab" data-bs-target="#seleccionarTorneo" type="button" role="tab">Seleccionar Torneo</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="crearTorneo-tab" data-bs-toggle="tab" data-bs-target="#crearTorneo" type="button" role="tab">Crear Nuevo Torneo</button>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Tab SeleccionarTorneo -->
                    <div class="tab-pane fade show active" id="seleccionarTorneo" role="tabpanel">
                        <form method="POST" action="{{ url('/admin/equipos/'.$equipo->id.'/torneos/agregar') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="torneo_existente" class="form-label">Torneo Existente</label>
                                <select class="form-select" id="torneo_existente" name="torneo_id" required>
                                    <option value="">Selecciona un torneo</option>
                                    @foreach($torneosDisponibles as $torneo)
                                    <option value="{{ $torneo->id }}">{{ $torneo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Añadir al Torneo</button>
                        </form>
                    </div>

                    <!-- Tab CrearTorneo -->
                    <div class="tab-pane fade" id="crearTorneo" role="tabpanel">
                        <form method="POST" action="{{ url('/admin/equipos/'.$equipo->id.'/torneos/crear') }}" enctype="multipart/form-data">
                            @csrf
                            <!-- <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="nuevo_nombre" class="form-label">Nombre del Torneo</label>
                                    <input type="text" class="form-control" id="nuevo_nombre" name="nombre" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="logo" class="form-label">Logo</label>
                                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="">Selecciona un estado</option>
                                        <option value="1" selected>Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div> -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label">Nombre del Torneo</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="logo" class="form-label">Logo</label>
                                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                </div>
                                <div class="col-12">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="1" selected>Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="jugadores_por_equipo" class="form-label">Jugadores por Equipo</label>
                                    <input type="number" class="form-control" id="jugadores_por_equipo" name="jugadores_por_equipo" min="1" value="5" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label d-block" for="usa_posiciones">Usar Posiciones</label>
                                    <input class="form-check-input ms-3 mt-3" type="checkbox" id="usa_posiciones" name="usa_posiciones" value="1" style="transform: scale(2);">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Crear y Añadir</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="jugadorModal" tabindex="-1" aria-labelledby="jugadorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="jugadorModalLabel">Gestionar Jugadores</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs mb-3" id="jugadorTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="seleccionarJugador-tab" data-bs-toggle="tab" data-bs-target="#seleccionarJugador" type="button" role="tab">Seleccionar Jugador</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="crearJugador-tab" data-bs-toggle="tab" data-bs-target="#crearJugador" type="button" role="tab">Crear Nuevo Jugador</button>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Tab SeleccionarJugador -->
                    <div class="tab-pane fade show active" id="seleccionarJugador" role="tabpanel">
                        <form method="POST" action="{{ url('/admin/equipos/'.$equipo->id.'/jugadores/agregar') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="jugador_existente" class="form-label">Jugador Existente</label>
                                <select class="form-select" id="jugador_existente" name="jugador_id" required>
                                    <option value="">Selecciona un jugador</option>
                                    @foreach($jugadoresDisponibles as $jugador)
                                    <option value="{{ $jugador->id }}">{{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Añadir Jugador</button>
                        </form>
                    </div>

                    <!-- Tab CrearJugador -->
                    <div class="tab-pane fade" id="crearJugador" role="tabpanel">
                        <form method="POST" action="{{ url('/admin/equipos/'.$equipo->id.'/jugadores/crear') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="nuevo_nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nuevo_nombre" name="nombre" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="apellido1" class="form-label">Primer Apellido</label>
                                    <input type="text" class="form-control" id="apellido1" name="apellido1" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="apellido2" class="form-label">Segundo Apellido</label>
                                    <input type="text" class="form-control" id="apellido2" name="apellido2" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="fecha_nacimiento" class="form-label">Fecha Nacimiento</label>
                                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="posicion" class="form-label">Posición</label>
                                    <input type="text" class="form-control" id="posicion" name="posicion">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="foto" class="form-label">Foto</label>
                                    <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Crear y Añadir</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('#tablaTorneosEquipo').DataTable({
            order: false,
            locale: "es",
            colReorder: true,
            dom: 'frtip',
            stateSave: true,
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print',
            ]
        });
        $('#tablaJugadoresEquipo').DataTable({
            order: false,
            locale: "es",
            colReorder: true,
            dom: 'frtip',
            stateSave: true,
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print',
            ]
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const botonesEliminar = document.querySelectorAll('.btn-desapuntar-equipo');
        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                Swal.fire({
                    title: '¿Estás seguro de que deseas expulsar este equipo del torneo?',
                    text: "El equipo no participará más en el torneo.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, expulsar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });

        const botonesExpulsarJugador = document.querySelectorAll('.btn-expulsar-jugador');
        botonesExpulsarJugador.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');

                Swal.fire({
                    title: '¿Estás seguro de que deseas expulsar a este jugador del equipo?',
                    text: "El jugador no formará parte del equipo.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, expulsar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });

        const botonesEliminarEquipo = document.querySelectorAll('.btn-eliminar-equipo');
        botonesEliminarEquipo.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar este equipo?',
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