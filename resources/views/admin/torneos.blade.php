@extends('admin.layouts.app')

@section('title', 'Torneos')

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
        <h1 class="mb-0">Torneos</h1>
        <!-- Botón para abrir el modal -->
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearTorneoModal">
            Crear Torneo
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table id="tablaTorneos" class="table table-striped table-hover align-middle mt-5 text-center">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Logo</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Descripción</th>
                    <th class="text-center">Fecha de Inicio</th>
                    <th class="text-center">Fecha de Fin</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($torneos as $torneo)
                <tr>
                    <td class="text-center">{{ $torneo->id }}</td>
                    <td class="text-center">
                        @if($torneo->logo)
                        <img src="{{ asset($torneo->logo) }}" alt="Logo" style="width: 50px; height: 50px; object-fit: contain;">
                        @else
                        <span class="text-muted">Sin logo</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $torneo->nombre }}</td>
                    <td class="text-center">{{ $torneo->descripcion }}</td>
                    <td class="text-center">{{ $torneo->fecha_inicio }}</td>
                    <td class="text-center">{{ $torneo->fecha_fin }}</td>
                    <td class="text-center">
                        @if($torneo->estado)
                        <span class="badge bg-success">Activo</span>
                        @else
                        <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ url('/admin/torneos/'.$torneo->id) }}" class="btn btn-sm btn-info" title="Ver torneo">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                        <a href="{{ url('/admin/torneos/'.$torneo->id.'/eliminar') }}"
                            class="btn btn-sm btn-danger btn-eliminar-torneo"
                            title="Eliminar torneo"
                            data-url="{{ url('/admin/torneos/'.$torneo->id.'/eliminar') }}">
                            <i class="bi bi-trash"></i> Eliminar
                        </a>
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
                    <button type="submit" class="btn btn-success">Crear</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const botonesEliminar = document.querySelectorAll('.btn-eliminar-torneo');

        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');

                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar este torneo?',
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
    });
</script>
@endpush