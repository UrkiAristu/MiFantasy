@extends('admin.layouts.app')

@section('title', 'Jugadores')

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
        <h1 class="mb-0">Jugadores</h1>
        <!-- Botón para abrir el modal -->
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearJugadorModal">
            Crear Jugador
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table id="tablaJugadores" class="table table-striped table-hover align-middle mt-5 text-center">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Foto</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Apellidos</th>
                    <th class="text-center">Fecha de Nacimiento</th>
                    <th class="text-center">Posición</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jugadores as $jugador)
                <tr>
                    <td class="text-center">{{ $jugador->id }}</td>
                    <td class="text-center">
                        @if($jugador->foto)
                        <img src="{{ asset($jugador->foto) }}" alt="Foto" style="width: 50px; height: 50px; object-fit: contain;">
                        @else
                        <span class="text-muted">Sin foto</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $jugador->nombre }}</td>
                    <td class="text-center">{{ $jugador->apellido1 }} {{ $jugador->apellido2 }}</td>
                    <td class="text-center">{{ $jugador->fecha_nacimiento }}</td>
                    <td class="text-center"><span class="text-muted">{{ $jugador->posicion ?? 'Sin posición' }}</span></td>
                    <td class="text-center">
                        <a href="{{ url('/admin/jugadores/'.$jugador->id) }}" class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                        <a href="{{ url('/admin/jugadores/'.$jugador->id.'/eliminar') }}"
                            class="btn btn-sm btn-danger btn-eliminar-jugador"
                            title="Eliminar jugador"
                            data-url="{{ url('/admin/jugadores/'.$jugador->id.'/eliminar') }}">
                            <i class="bi bi-trash"></i> Eliminar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No hay jugadores creados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal para crear jugador -->
    <div class="modal fade" id="crearJugadorModal" tabindex="-1" aria-labelledby="crearJugadorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ url('/admin/jugadores/crear') }}" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="crearJugadorModalLabel">Crear Jugador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Jugador</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="apellido1" class="form-label">Primer Apellido</label>
                        <input type="text" class="form-control" id="apellido1" name="apellido1" required>
                    </div>
                    <div class="mb-3">
                        <label for="apellido2" class="form-label">Segundo Apellido</label>
                        <input type="text" class="form-control" id="apellido2" name="apellido2" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                    </div>
                    <div class="mb-3">
                        <label for="posicion" class="form-label">Posición</label>
                        <input type="text" class="form-control" id="posicion" name="posicion">
                    </div>
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Crear</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('#tablaJugadores').DataTable({
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
        const botonesEliminar = document.querySelectorAll('.btn-eliminar-jugador');

        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.dataset.url;

                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar este jugador?',
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