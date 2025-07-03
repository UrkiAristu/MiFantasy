@extends('admin.layouts.app')

@section('title', 'Equipos')

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
        <h1 class="mb-0">Equipos</h1>
        <!-- Botón para abrir el modal -->
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearEquipoModal">
            Crear Equipo
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table id="tablaEquipos" class="table table-striped table-hover align-middle mt-5 text-center">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Logo</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Fecha de Creación</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipos as $equipo)
                <tr>
                    <td class="text-center">{{ $equipo->id }}</td>
                    <td class="text-center">
                        @if($equipo->logo)
                        <img src="{{ asset($equipo->logo) }}" alt="Logo" style="width: 50px; height: 50px; object-fit: contain;">
                        @else
                        <span class="text-muted">Sin logo</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $equipo->nombre }}</td>
                    <td class="text-center">{{ $equipo->created_at }}</td>
                    <td class="text-center">
                        <a href="{{ url('/admin/equipos/'.$equipo->id) }}" class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                        <a href="{{ url('/admin/equipos/'.$equipo->id.'/eliminar') }}"
                            class="btn btn-sm btn-danger btn-eliminar-equipo"
                            title="Eliminar equipo"
                            data-url="{{ url('/admin/equipos/'.$equipo->id.'/eliminar') }}">
                            <i class="bi bi-trash"></i> Eliminar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No hay equipos creados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal para crear equipo -->
    <div class="modal fade" id="crearEquipoModal" tabindex="-1" aria-labelledby="crearEquipoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ url('/admin/equipos/crear') }}" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="crearEquipoModalLabel">Crear Equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Equipo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
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
        $('#tablaEquipos').DataTable({
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
        const botonesEliminar = document.querySelectorAll('.btn-eliminar-equipo');

        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.dataset.url;

                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar este equipo?',
                    text: "¡Esta acción no se puede deshacer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirige
                        window.location.href = url;
                    }
                });
            });
        });
    });
</script>

@endpush