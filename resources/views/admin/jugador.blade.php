@extends('admin.layouts.app')

@section('title', 'Detalle del Jugador')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Detalle del Jugador</h1>

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
            <h5 class="card-title">Información del Jugador</h5>
            <form method="POST" action="{{ url('/admin/jugadores/'.$jugador->id.'/editar') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Columna izquierda: Foto Actual -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Foto Actual</label><br>
                        @if($jugador->foto)
                        <img src="{{ asset($jugador->foto) }}"
                            alt="Foto del jugador"
                            class="img-thumbnail"
                            style="max-width: 160px; height: auto;">
                        <!-- Checkbox para eliminar foto -->
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="eliminar_foto" id="eliminar_foto" value="1">
                            <label class="form-check-label" for="eliminar_foto">
                                Eliminar foto actual
                            </label>
                        </div>
                        @else
                        <p class="text-muted">Sin foto</p>
                        @endif
                    </div>

                    <!-- Columna derecha: Formulario -->
                    <div class="col-md-9">
                        <!-- Fila 1: Cambiar Foto + Nombre -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="foto" class="form-label">Cambiar Foto</label>
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $jugador->nombre) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellido1" class="form-label">Primer Apellido</label>
                                <input type="text" class="form-control" id="apellido1" name="apellido1" value="{{ old('apellido1', $jugador->apellido1) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellido2" class="form-label">Segundo Apellido</label>
                                <input type="text" class="form-control" id="apellido2" name="apellido2" value="{{ old('apellido2', $jugador->apellido2) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $jugador->fecha_nacimiento) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="posicion" class="form-label">Posición</label>
                                <input type="text" class="form-control" id="posicion" name="posicion" value="{{ old('posicion', $jugador->posicion) }}">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">Guardar Cambios</button>
                        <a href="{{ url('/admin/jugadores/'.$jugador->id.'/eliminar') }}"
                            class="btn btn-danger btn-eliminar-jugador"
                            title="Eliminar el jugador"
                            data-url="{{ url('/admin/jugadores/'.$jugador->id.'/eliminar') }}">
                            Eliminar
                        </a>
                        <a href="{{ url('/admin/equipos') }}" class="btn btn-secondary">Volver</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Card Equipos  -->
    <div class="card mt-5">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Equipos</h5>
                <!-- Botón para abrir modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#equipoModal">
                    Añadir Equipos
                </button>
            </div>

            @if($jugador->equipos->count())
            <table id="tablaEquiposJugador" class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Logo</th>
                        <th class="text-center">Nombre del Equipo</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jugador->equipos as $index => $equipo)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">
                            @if($equipo->logo)
                            <img src="{{ asset($equipo->logo) }}" alt="Logo" style="width:40px; height:40px; object-fit:contain;">
                            @else
                            <span class="text-muted">Sin logo</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $equipo->nombre }}</td>
                        <td class="text-center">
                            <a href="{{ url('/admin/equipos/'.$equipo->id) }}" class="btn btn-info btn-sm" title="Ver equipo">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <a href="{{ url('/admin/jugadores/'.$jugador->id.'/equipos/'.$equipo->id.'/eliminar') }}"
                                class="btn btn-danger btn-sm btn-dejar-equipo"
                                title="Desapuntar del torneo"
                                data-url="{{ url('/admin/jugadores/'.$jugador->id.'/equipos/'.$equipo->id.'/eliminar') }}">
                                <i class="bi bi-x-circle"></i> Quitar
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-muted mb-0">No hay equipos todavía.</p>
            @endif
        </div>
    </div>
    <!-- Card Estadisticas -->
    <div class="card mt-5">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Estadisticas</h5>
            </div>

            @if($jugador->participaciones->count())
            <table id="tablaTorneosJugador" class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Torneo</th>
                        <th class="text-center">Equipo</th>
                        <th class="text-center">Goles</th>
                        <th class="text-center">Asistencias</th>
                        <th class="text-center">Puntos</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jugador->participaciones as $index => $torneo)
                    @php
                    $equipo = \App\Models\Equipo::find($torneo->pivot->equipo_id);
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>

                        <!-- Torneo: Logo + Nombre -->
                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center">
                                @if($torneo->logo)
                                <img src="{{ asset($torneo->logo) }}" alt="Logo Torneo" style="width:40px; height:40px; object-fit:contain;">
                                @else
                                <span class="text-muted d-block">Sin logo</span>
                                @endif
                                <small class="mt-1">{{ $torneo->nombre }}</small>
                            </div>
                        </td>

                        <!-- Equipo: Logo + Nombre -->
                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center">
                                @if($equipo && $equipo->logo)
                                <img src="{{ asset($equipo->logo) }}" alt="Logo Equipo" style="width:40px; height:40px; object-fit:contain;">
                                @else
                                <span class="text-muted d-block">Sin logo</span>
                                @endif
                                <small class="mt-1">{{ $equipo ? $equipo->nombre : 'Sin equipo' }}</small>
                            </div>
                        </td>

                        <td class="text-center">{{ $torneo->pivot->goles }}</td>
                        <td class="text-center">{{ $torneo->pivot->asistencias }}</td>
                        <td class="text-center">{{ $torneo->pivot->puntos }}</td>

                        <td class="text-center">
                            <a href="{{ url('/admin/torneos/'.$torneo->id) }}" class="btn btn-info btn-sm" title="Ver torneo">
                                <i class="bi bi-trophy"></i> Torneo
                            </a>
                            <a href="{{ url('/admin/equipos/'.$equipo->id) }}" class="btn btn-dark btn-sm" title="Ver equipo">
                                <i class="bi bi-shield"></i> Equipo
                            </a>
                            <a href="{{ url('/admin/torneos/'.$torneo->id.'/equipos/'.$equipo->id.'/jugadores') }}" class="btn btn-warning btn-sm" title="Ver plantilla">
                                <i class="bi bi-people"></i> Plantilla
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-muted mb-0">No ha participado en ningún torneo.</p>
            @endif

        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="equipoModal" tabindex="-1" aria-labelledby="equipoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="equipoModalLabel">Gestionar Equipos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs mb-3" id="equipoTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="seleccionar-tab" data-bs-toggle="tab" data-bs-target="#seleccionar" type="button" role="tab">Seleccionar Equipo</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="crear-tab" data-bs-toggle="tab" data-bs-target="#crear" type="button" role="tab">Crear Nuevo Equipo</button>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Tab Seleccionar -->
                    <div class="tab-pane fade show active" id="seleccionar" role="tabpanel">
                        <form method="POST" action="{{ url('/admin/jugadores/'.$jugador->id.'/equipos/agregar') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="equipo_existente" class="form-label">Equipo Existente</label>
                                <select class="form-select" id="equipo_existente" name="equipo_id" required>
                                    <option value="">Selecciona un equipo</option>
                                    @foreach($equiposDisponibles as $equipo)
                                    <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Añadir al Torneo</button>
                        </form>
                    </div>

                    <!-- Tab Crear -->
                    <div class="tab-pane fade" id="crear" role="tabpanel">
                        <form method="POST" action="{{ url('/admin/jugadores/'.$jugador->id.'/equipos/crear') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="nuevo_nombre" class="form-label">Nombre del Equipo</label>
                                <input type="text" class="form-control" id="nuevo_nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
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
        $('#equipo_existente').select2({
            width: '100%',
            dropdownParent: $('#equipoModal'),
            placeholder: 'Selecciona un equipo',
            allowClear: true,
            language: {
                noResults: function () {
                    return "No se encontraron equipos";
                }
            }
        });
        $('#tablaEquiposJugador').DataTable({
            order: false,
            locale: "es",
            colReorder: true,
            dom: 'frtip',
            stateSave: true,
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print',
            ]
        });
        $('#tablaTorneosJugador').DataTable({
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
        const botonesEliminar = document.querySelectorAll('.btn-dejar-equipo');
        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                Swal.fire({
                    title: '¿Estás seguro de que deseas expulsar este jugador del equipo?',
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

        const botonEliminarJugador = document.querySelector('.btn-eliminar-jugador');
        botonEliminarJugador.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            Swal.fire({
                title: '¿Estás seguro de que deseas eliminar este jugador?',
                text: "Esta acción no se puede deshacer.",
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
</script>
@endpush