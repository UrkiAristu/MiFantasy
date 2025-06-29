@extends('admin.layouts.app')

@section('title', 'Torneos Creados')

@section('content')
<div class="container mt-5">
    <!-- Mensajes -->
    @if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
        {{ $error }}<br>
        @endforeach
    </div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Torneos Creados</h1>
        <!-- Botón para abrir el modal -->
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearTorneoModal">
            Crear Torneo
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table id="tablaTorneos" class="table table-striped table-hover align-middle mt-5">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Logo</th>
                    <th>Nombre del Torneo</th>
                    <th>Descripción</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Fin</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($torneos as $torneo)
                <tr>
                    <td>{{ $torneo->id }}</td>
                    <td>
                        @if($torneo->logo)
                        <img src="{{ asset($torneo->logo) }}" alt="Logo" style="width: 50px; height: 50px; object-fit: contain;">
                        @else
                        <span class="text-muted">Sin logo</span>
                        @endif
                    </td>
                    <td>{{ $torneo->nombre }}</td>
                    <td>{{ $torneo->descripcion }}</td>
                    <td>{{ $torneo->fecha_inicio }}</td>
                    <td>{{ $torneo->fecha_fin }}</td>
                    <td>
                        @if($torneo->estado)
                        <span class="badge bg-success">Activo</span>
                        @else
                        <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ url('/admin/torneos/'.$torneo->id) }}" class="btn btn-sm btn-outline-info">Ver</a>
                        <a href="{{ url('/admin/torneos/'.$torneo->id.'/eliminar') }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No hay torneos creados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal para crear torneo -->
    <div class="modal fade" id="crearTorneoModal" tabindex="-1" aria-labelledby="crearTorneoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ url('/admin/torneos/crear') }}" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="crearTorneoModalLabel">Crear Torneo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Torneo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                    </div>
                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="1" selected>Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('#tablaTorneos').DataTable({
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
@endpush