@extends('admin.layouts.app')

@section('title', 'Detalle del Torneo')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Detalle del Torneo</h1>

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
            <h5 class="card-title">Información del Torneo</h5>
            <form method="POST" action="{{ url('/admin/torneos/'.$torneo->id.'/editar') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Columna izquierda: Logo Actual -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Logo Actual</label><br>
                        @if($torneo->logo)
                        <img src="{{ asset($torneo->logo) }}"
                            alt="Logo del torneo"
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
                                <label for="nombre" class="form-label">Nombre del Torneo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $torneo->nombre) }}" required>
                            </div>
                        </div>

                        <!-- Fila 2: Estado + Descripción -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="2">{{ old('descripcion', $torneo->descripcion) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fechas: debajo ocupando toda la tarjeta -->
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                            value="{{ old('fecha_inicio', $torneo->fecha_inicio) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin"
                            value="{{ old('fecha_fin', $torneo->fecha_fin) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="1" {{ old('estado', $torneo->estado) == 1 ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ old('estado', $torneo->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    <a href="{{ url('/admin/torneos') }}" class="btn btn-secondary">Volver</a>
                </div>
            </form>
        </div>
    </div>
    <div class="card mt-5">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Equipos Inscritos</h5>
                <!-- Botón para abrir modal -->
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#equipoModal">
                    Añadir Equipo
                </button>
            </div>

            @if($torneo->equipos->count())
            <table id="tablaEquiposTorneo" class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Logo</th>
                        <th class="text-center">Nombre del Equipo</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($torneo->equipos as $index => $equipo)
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
                            <a href="{{ url('/admin/torneos/'.$torneo->id.'/equipos/'.$equipo->id.'/eliminar') }}"
                                class="btn btn-danger btn-sm btn-desapuntar-equipo"
                                title="Desapuntar del torneo"
                                data-url="{{ url('/admin/torneos/'.$torneo->id.'/equipos/'.$equipo->id.'/eliminar') }}">
                                <i class="bi bi-x-circle"></i> Quitar
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-muted mb-0">No hay equipos inscritos todavía.</p>
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
                        <form method="POST" action="{{ url('/admin/torneos/'.$torneo->id.'/equipos/agregar') }}">
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
                        <form method="POST" action="{{ url('/admin/torneos/'.$torneo->id.'/equipos/crear') }}">
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
        $('#tablaEquiposTorneo').DataTable({
            order: false,
            locale: "es",
            colReorder: true,
            dom: 'Bfrtip',
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
                    title: '¿Estás seguro de que deseas eliminar este equipo del torneo?',
                    text: "El equipo no participará más en el torneo.",
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
    });
</script>
@endpush