@extends('admin.layouts.app')

@section('title', 'Plantilla de Equipo en Torneo')

@section('content')
<div class="container mt-5">
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

    <div class="row align-items-center mb-5">
        <!-- Logo Equipo -->
        <div class="col-md-6 text-center">
            <a href="{{ url('/admin/equipos/'.$equipo->id) }}" class="text-decoration-none text-dark">
                @if($equipo->logo)
                <img src="{{ asset($equipo->logo) }}" alt="Logo Equipo" class="img-fluid mb-2" style="max-height: 200px;">
                @else
                <p class="text-muted">Sin logo del equipo</p>
                @endif
                <h3 class="mt-2">{{ $equipo->nombre }}</h3>
            </a>
        </div>

        <!-- Logo Torneo -->
        <div class="col-md-6 text-center">
            <a href="{{ url('/admin/torneos/'.$torneo->id) }}" class="text-decoration-none text-dark">
                @if($torneo->logo)
                <img src="{{ asset($torneo->logo) }}" alt="Logo Torneo" class="img-fluid mb-2" style="max-height: 200px;">
                @else
                <p class="text-muted">Sin logo del torneo</p>
                @endif
                <h3 class="mt-2">{{ $torneo->nombre }}</h3>
            </a>
        </div>
    </div>


    <!-- Tabla Jugadores -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Jugadores inscritos</h5>
                <!-- Botón para abrir modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inscripcionModal">
                    Inscribir Jugador
                </button>
            </div>

            @if($jugadores->count())
            <table id="tablaJugadores" class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Posición</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jugadores as $index => $jugador)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($jugador->foto)
                            <img src="{{ asset($jugador->foto) }}" alt="Foto Jugador" style="width:50px; height:50px; object-fit:cover;">
                            @else
                            <span class="text-muted">Sin foto</span>
                            @endif
                        </td>
                        <td>{{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}</td>
                        <td>{{ $jugador->posicion }}</td>
                        <td>
                            <a href="{{ url('/admin/jugadores/'.$jugador->id) }}" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <a href="{{ url('/admin/torneos/'.$torneo->id.'/equipos/'.$equipo->id.'/jugadores/'.$jugador->id.'/eliminar') }}"
                                class="btn btn-danger btn-sm btn-desinscribir-jugador"
                                title="Desinscribir del torneo"
                                data-url="{{ url('/admin/torneos/'.$torneo->id.'/equipos/'.$equipo->id.'/jugadores/'.$jugador->id.'/eliminar') }}">
                                <i class="bi bi-x-circle"></i> Quitar
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-muted mb-0">No hay jugadores inscritos todavía.</p>
            @endif
        </div>
    </div>
</div>
<!-- Modal para inscribir jugador -->
<div class="modal fade" id="inscripcionModal" tabindex="-1" aria-labelledby="inscripcionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inscripcionModalLabel">Inscribir Jugadores</h5>
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
                        <form method="POST" action="{{ url('/admin/torneos/'.$torneo->id.'/equipos/'.$equipo->id.'/jugadores/agregar') }}">
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
                        <form method="POST" action="{{ url('/admin/torneos/'.$torneo->id.'/equipos/'.$equipo->id.'/jugadores/crear') }}" enctype="multipart/form-data">
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
        const botonesDesinscribir = document.querySelectorAll('.btn-desinscribir-jugador');
        botonesDesinscribir.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                Swal.fire({
                    title: '¿Estás seguro de que deseas desinscribir a este jugador del torneo?',
                    text: "El jugador no formará parte del torneo.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, desinscribir',
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