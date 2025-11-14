@extends('admin.layouts.app')

@section('title', 'Detalle del Partido')

@section('content')
<div class="container mt-5">
    <!-- Título -->
    <div class="text-center mb-4">
        <h1>Detalle del Partido</h1>
        <a href="{{ url('/admin/torneos/'.$partido->jornada->torneo->id.'/jornadas') }}">
            <h3>{{ $partido->jornada->torneo->nombre }}</h3>
                <h5>{{ $partido->jornada->nombre }}</h5>
        </a>
    </div>

    <!-- Equipos y marcador -->
    <div class="row align-items-center text-center mb-5">
        <div class="col-md-5">
            <a href="{{ url('/admin/equipos/'.$partido->equipoLocal->id) }}">
                @if($partido->equipoLocal->logo)
                <img src="{{ asset($partido->equipoLocal->logo) }}" alt="Logo Local" class="img-fluid mb-2" style="max-height: 100px;">
                @endif
                <h3>{{ $partido->equipoLocal->nombre }}</h3>
            </a>
        </div>
        <div class="col-md-2">
            <h2 class="fw-bold">{{ $partido->goles_local ?? '-' }} - {{ $partido->goles_visitante ?? '-' }}</h2>
            <p class="text-muted">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('d/m/Y') }} {{ \Carbon\Carbon::parse($partido->fecha_partido)->format('H:i') }}</p>
        </div>
        <div class="col-md-5">
            <a href="{{ url('/admin/equipos/'.$partido->equipoVisitante->id) }}">
                @if($partido->equipoVisitante->logo)
                <img src="{{ asset($partido->equipoVisitante->logo) }}" alt="Logo Visitante" class="img-fluid mb-2" style="max-height: 100px;">
                @endif
                <h3>{{ $partido->equipoVisitante->nombre }}</h3>
            </a>
        </div>
    </div>
    <!-- Mensajes -->
    @if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
        {{ $error }}<br>
        @endforeach
    </div>
    @endif
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <!-- Eventos (Timeline) -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Cronología de Eventos</h5>
                <div>
                    <!-- Botón Gestionar Eventos -->
                    <button type="button"
                        class="btn btn-info"
                        data-bs-toggle="modal"
                        data-bs-target="#eventosPartidoModal"
                        data-partido-id="{{ $partido->id }}"
                        data-eventos='@json($partido->eventos ?? [])'>
                        Gestionar Eventos
                    </button>
                    <!-- Botón Editar Partido -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editarPartidoModal">
                        Editar Partido
                    </button>
                    <a href="{{ url('/admin/partidos/'.$partido->id.'/eliminar') }}"
                        class="btn btn-danger btn-eliminar-partido"
                        title="Eliminar el partido"
                        data-url="{{ url('/admin/partidos/'.$partido->id.'/eliminar') }}">
                        Eliminar
                    </a>
                </div>
            </div>

            @php
            $eventos = json_decode($partido->eventos);
            $eventos = collect($eventos)->sortBy('minuto'); // ordenar por minuto
            @endphp

            @if($eventos && $eventos->count())
            <ul class="list-group">
                @foreach($eventos as $evento)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <strong>{{ $evento->minuto }}'</strong> -
                        {{ $evento->tipo }}
                        (<a href="{{ url('/admin/equipos/' . ($evento->equipo_id ?? '')) }}">{{ ucfirst($evento->equipo_nombre) }}</a>)
                        @if(!empty($evento->jugador_nombre))
                        - <a href="{{ url('/admin/jugadores/' . ($evento->jugador_id ?? '')) }}">{{ $evento->jugador_nombre }}</a>
                        @endif
                    </span>
                    @if($evento->tipo == 'Gol')
                    <img src="{{ asset('assets/media/icons/gol.png') }}" alt="Gol" class="me-2" style="height: 24px;">
                    @elseif($evento->tipo == 'Asistencia')
                    <img src="{{ asset('assets/media/icons/asistencia.png') }}" alt="Asistencia" class="me-2" style="height: 24px;">
                    @elseif($evento->tipo == 'Tarjeta Roja')
                    <img src="{{ asset('assets/media/icons/tarjeta_roja.png') }}" alt="Tarjeta Roja" class="me-2" style="height: 24px;">
                    @elseif($evento->tipo == 'Tarjeta Amarilla')
                    <img src="{{ asset('assets/media/icons/tarjeta_amarilla.png') }}" alt="Tarjeta Amarilla" class="me-2" style="height: 24px;">
                    @elseif($evento->tipo == 'Falta')
                    <img src="{{ asset('assets/media/icons/falta.png') }}" alt="Falta" class="me-2" style="height: 24px;">
                    @elseif($evento->tipo == 'Parada')
                    <img src="{{ asset('assets/media/icons/parada.png') }}" alt="Falta" class="me-2" style="height: 24px;">
                    @else
                    <i class="bi bi-info-circle fs-5 text-primary"></i>
                    @endif
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-muted mb-0">No hay eventos registrados para este partido.</p>
            @endif
        </div>
    </div>
    <!-- Tablas estadisticas -->
    <div class="row g-4 mt-4">
        {{-- LOCAL --}}
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                {{-- HEADER --}}
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <div class="d-flex align-items-center">
                        @if($partido->equipoLocal?->logo)
                            <img src="{{ asset($partido->equipoLocal->logo) }}"
                                alt="Logo {{ $partido->equipoLocal->nombre }}"
                                class="me-2" style="height:32px">
                        @endif
                        <h5 class="mb-0 fw-bold">
                            Estadísticas — {{ $partido->equipoLocal?->nombre }}
                        </h5>
                    </div>
                </div>
                {{-- BODY --}}
                <div class="card-body">
                    @if ($statsLocal->count())
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered rounded-3 overflow-hidden mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jugador</th>
                                        <th class="text-center">Pos</th>
                                        <th class="text-center">Min</th>
                                        <th class="text-center">G</th>
                                        <th class="text-center">A</th>
                                        <th class="text-center">TA</th>
                                        <th class="text-center">TR</th>
                                        <th class="text-center">F</th>
                                        <th class="text-center">Par</th>
                                        <th class="text-end">Pts</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($statsLocal as $stat)
                                        <tr>
                                            <td>{{ $stat->jugador?->nombre }} {{ $stat->jugador?->apellido1 }}</td>
                                            <td class="text-center">{{ $stat->posicion ?? '—' }}</td>
                                            <td class="text-center">{{ $stat->minutos }}</td>
                                            <td class="text-center">{{ $stat->goles }}</td>
                                            <td class="text-center">{{ $stat->asistencias }}</td>
                                            <td class="text-center">{{ $stat->tarjetas_amarillas }}</td>
                                            <td class="text-center">{{ $stat->tarjetas_rojas }}</td>
                                            <td class="text-center">{{ $stat->faltas }}</td>
                                            <td class="text-center">{{ $stat->paradas }}</td>
                                            <td class="text-end fw-bold">{{ $stat->puntos }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">No hay estadísticas disponibles.</div>
                    @endif
                </div>
            </div>
        </div>
        {{-- VISITANTE --}}
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                {{-- HEADER --}}
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <div class="d-flex align-items-center">
                        @if($partido->equipoVisitante?->logo)
                            <img src="{{ asset($partido->equipoVisitante->logo) }}"
                                alt="Logo {{ $partido->equipoVisitante->nombre }}"
                                class="me-2" style="height:32px">
                        @endif
                        <h5 class="mb-0 fw-bold">
                            Estadísticas — {{ $partido->equipoVisitante?->nombre }}
                        </h5>
                    </div>
                </div>
                {{-- BODY --}}
                <div class="card-body">
                    @if ($statsVisitante->count())
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered rounded-3 overflow-hidden mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jugador</th>
                                        <th class="text-center">Pos</th>
                                        <th class="text-center">Min</th>
                                        <th class="text-center">G</th>
                                        <th class="text-center">A</th>
                                        <th class="text-center">TA</th>
                                        <th class="text-center">TR</th>
                                        <th class="text-center">F</th>
                                        <th class="text-center">Par</th>
                                        <th class="text-end">Pts</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($statsVisitante as $stat)
                                        <tr>
                                            <td>{{ $stat->jugador?->nombre }} {{ $stat->jugador?->apellido1 }}</td>
                                            <td class="text-center">{{ $stat->posicion ?? '—' }}</td>
                                            <td class="text-center">{{ $stat->minutos }}</td>
                                            <td class="text-center">{{ $stat->goles }}</td>
                                            <td class="text-center">{{ $stat->asistencias }}</td>
                                            <td class="text-center">{{ $stat->tarjetas_amarillas }}</td>
                                            <td class="text-center">{{ $stat->tarjetas_rojas }}</td>
                                            <td class="text-center">{{ $stat->faltas }}</td>
                                            <td class="text-center">{{ $stat->paradas }}</td>
                                            <td class="text-end fw-bold">{{ $stat->puntos }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">No hay estadísticas disponibles.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Botón para volver -->
    <div class="mt-4">
        <a href="{{ url('/admin/torneos/'.$partido->jornada->torneo->id.'/jornadas') }}" class="btn btn-secondary">Volver al Torneo</a>
    </div>
</div>
<!-- Modal Editar Partido -->
<div class="modal fade" id="editarPartidoModal" tabindex="-1" aria-labelledby="editarPartidoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ url('/admin/partidos/'.$partido->id.'/editar') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="editarPartidoModalLabel">Editar Partido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <!-- Equipo Local -->
                    <div class="col-md-6 mb-3">
                        <label for="editarEquipoLocal" class="form-label">Equipo Local</label>
                        <select name="equipo_local_id" id="editarEquipoLocal" class="form-select" required>
                            @foreach($equipos as $equipo)
                            <option value="{{ $equipo->id }}"
                                {{ $partido->equipo_local_id == $equipo->id ? 'selected' : '' }}>
                                {{ $equipo->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Equipo Visitante -->
                    <div class="col-md-6 mb-3">
                        <label for="editarEquipoVisitante" class="form-label">Equipo Visitante</label>
                        <select name="equipo_visitante_id" id="editarEquipoVisitante" class="form-select" required>
                            @foreach($equipos as $equipo)
                            <option value="{{ $equipo->id }}"
                                {{ $partido->equipo_visitante_id == $equipo->id ? 'selected' : '' }}>
                                {{ $equipo->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Goles Local -->
                    <div class="col-md-4 mb-3">
                        <label for="editarGolesLocal" class="form-label">Goles Local</label>
                        <input type="number" name="goles_local" id="editarGolesLocal" class="form-control"
                            value="{{ $partido->goles_local ?? '' }}" min="0" required>
                    </div>

                    <!-- Goles Visitante -->
                    <div class="col-md-4 mb-3">
                        <label for="editarGolesVisitante" class="form-label">Goles Visitante</label>
                        <input type="number" name="goles_visitante" id="editarGolesVisitante" class="form-control"
                            value="{{ $partido->goles_visitante ?? '' }}" min="0" required>
                    </div>

                    <!-- Estado -->
                    <div class="col-md-4 mb-3">
                        <label for="editarEstado" class="form-label">Estado</label>
                        <select name="estado" id="editarEstado" class="form-select">
                            <option value="programado" {{ $partido->estado == 'programado' ? 'selected' : '' }}>Programado</option>
                            <option value="jugado" {{ $partido->estado == 'jugado' ? 'selected' : '' }}>Jugado</option>
                            <option value="suspendido" {{ $partido->estado == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                            <option value="cancelado" {{ $partido->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>

                    <!-- Fecha -->
                    <div class="col-md-6 mb-3">
                        <label for="editarFecha" class="form-label">Fecha</label>
                        <input type="date" name="fecha_partido" id="editarFecha" class="form-control"
                            value="{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('Y-m-d') }}"
                            min="{{ \Carbon\Carbon::parse($partido->jornada->torneo->fecha_inicio)->format('Y-m-d') }}"
                            max="{{ \Carbon\Carbon::parse($partido->jornada->torneo->fecha_fin)->format('Y-m-d') }}" required>
                    </div>

                    <!-- Hora -->
                    <div class="col-md-6 mb-3">
                        <label for="editarHora" class="form-label">Hora</label>
                        <input type="time" name="hora_partido" id="editarHora" class="form-control"
                            value="{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('H:i') }}">
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Gestionar Eventos -->
<div class="modal fade" id="eventosPartidoModal" tabindex="-1" aria-labelledby="eventosPartidoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ url('/admin/partidos/'.$partido->id.'/eventos/crear') }}" class="modal-content">
            @csrf
            <input type="hidden" name="partido_id" id="eventosPartidoId">
            <input type="hidden" name="eventos_json" id="eventos_json">

            <div class="modal-header">
                <h5 class="modal-title" id="eventosPartidoModalLabel">Gestionar Eventos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <!-- Tipo -->
                    <div class="col-md-3 mb-3">
                        <label for="tipoEvento" class="form-label">Tipo</label>
                        <select id="tipoEvento" class="form-select">
                            <option value="">Selecciona tipo</option>
                            <option value="Gol">Gol</option>
                            <option value="Asistencia">Asistencia</option>
                            <option value="Falta">Falta</option>
                            <option value="Tarjeta Amarilla">Tarjeta Amarilla</option>
                            <option value="Tarjeta Roja">Tarjeta Roja</option>
                            <option value="Parada">Parada</option>
                        </select>
                    </div>

                    <!-- Minuto -->
                    <div class="col-md-2 mb-3">
                        <label for="minutoEvento" class="form-label">Minuto</label>
                        <input type="number" id="minutoEvento" class="form-control" min="0" max="120">
                    </div>

                    <!-- Equipo -->
                    <div class="col-md-3 mb-3">
                        <label for="equipoEvento" class="form-label">Equipo</label>
                        <select id="equipoEvento" name="equipo_id" class="form-select">
                            <option value="">Selecciona equipo</option>
                            @foreach ($equipos as $equipo)
                            <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
                            @endforeach
                        </select>

                    </div>

                    <!-- Jugador -->
                    <div class="col-md-4 mb-3">
                        <label for="jugadorEvento" class="form-label">Jugador</label>
                        <select id="jugadorEvento" class="form-select">
                            <option value="">Selecciona jugador</option>
                            @foreach ($jugadores as $jugador)
                            <option value="{{ $jugador->id }}" data-equipo-id="{{ $jugador->equipo_id }}">
                                {{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Botón añadir -->
                <div class="mb-3">
                    <button type="button" class="btn btn-primary" onclick="agregarEvento()">Añadir Evento</button>
                </div>

                <!-- Lista -->
                <ul class="list-group" id="listaEventos"></ul>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="location.reload();">Actualizar Página</button>
            </div>
        </form>
    </div>
</div>
@php
$eventosArray = collect(json_decode($partido->eventos, true) ?? [])->sortBy('minuto')->values()->all();
@endphp

@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const botonesEliminarPartido = document.querySelectorAll('.btn-eliminar-partido');
        botonesEliminarPartido.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar este partido?',
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
<script>
    let jugadoresLocal = <?= json_encode($jugadoresLocal) ?>,
        jugadoresVisitante = <?= json_encode($jugadoresVisitante) ?>,
        jugadores = <?= json_encode($jugadores) ?>;
    // jugadoresData debe ser un array plano con id, nombre, apellido1, apellido2 y equipo_id
    const jugadoresData = jugadores.map(j => ({
        id: j.id,
        nombre: j.nombre,
        apellido1: j.apellido1,
        apellido2: j.apellido2,
        equipo_id: j.equipo_id
    }));
</script>
<script>
    const selectEquipo = document.getElementById('equipoEvento');
    const selectJugador = document.getElementById('jugadorEvento');

    // Filtra jugadores según equipo elegido
    function filtrarJugadoresPorEquipo(equipoId) {
        selectJugador.innerHTML = '<option value="">Selecciona jugador</option>';

        if (!equipoId) {
            jugadoresData.forEach(j => {
                const opt = document.createElement('option');
                opt.value = j.id;
                opt.textContent = j.nombre + ' ' + j.apellido1 + ' ' + j.apellido2;
                selectJugador.appendChild(opt);
            });
            return;
        }

        jugadoresData.forEach(j => {
            if (j.equipo_id == equipoId) {
                const opt = document.createElement('option');
                opt.value = j.id;
                opt.textContent = j.nombre + ' ' + j.apellido1 + ' ' + j.apellido2;
                selectJugador.appendChild(opt);
            }
        });
    }

    // ✅ Selecciona equipo según jugador elegido
    function seleccionarEquipoPorJugador(jugadorId) {
        const jugador = jugadoresData.find(j => j.id == jugadorId);
        if (!jugador) return;

        selectEquipo.value = jugador.equipo_id;
        filtrarJugadoresPorEquipo(jugador.equipo_id);
        selectJugador.value = jugadorId; // Re-selecciona jugador
    }

    // Cambiar equipo ➜ filtrar jugadores
    selectEquipo.addEventListener('change', () => {
        filtrarJugadoresPorEquipo(selectEquipo.value);
    });

    // Cambiar jugador ➜ seleccionar su equipo
    selectJugador.addEventListener('change', () => {
        seleccionarEquipoPorJugador(parseInt(selectJugador.value));
    });
</script>
<script>
    let eventos = [];

    function agregarEvento() {
        const tipo = document.getElementById('tipoEvento').value;
        const minuto = document.getElementById('minutoEvento').value;
        const equipoId = document.getElementById('equipoEvento').value;
        const jugadorId = document.getElementById('jugadorEvento').value;

        if (!tipo || !minuto || !equipoId || !jugadorId) {
            Swal.fire({
                icon: 'error',
                title: 'Campos incompletos',
                text: 'Por favor completa todos los campos.'
            });
            return;
        }

        const jugador = jugadoresData.find(j => j.id == jugadorId);
        const equipo = selectEquipo.options[selectEquipo.selectedIndex].text;

        const evento = {
            tipo: tipo,
            minuto: parseInt(minuto),
            equipo_id: parseInt(equipoId),
            equipo_nombre: equipo,
            jugador_id: parseInt(jugadorId),
            jugador_nombre: jugador.nombre + ' ' + jugador.apellido1 + ' ' + jugador.apellido2
        };

        eventos.push(evento);

        guardarEventosAjax();
    }

    function actualizarListaEventos() {
        const lista = document.getElementById('listaEventos');
        lista.innerHTML = '';

        eventos.forEach((evento, index) => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.textContent = `[${evento.minuto}'] ${evento.tipo} - ${evento.jugador_nombre} (${evento.equipo_nombre})`;

            const btnEliminar = document.createElement('button');
            btnEliminar.type = 'button';
            btnEliminar.className = 'btn btn-sm btn-danger';
            btnEliminar.innerHTML = '<i class="bi bi-x-lg p-0"></i>';
            btnEliminar.onclick = function() {
                eventos.splice(index, 1);
                guardarEventosAjax();
            };

            li.appendChild(btnEliminar);
            lista.appendChild(li);
        });

        document.getElementById('eventos_json').value = JSON.stringify(eventos);
    }

    function guardarEventosAjax() {
        const partidoId = document.getElementById('eventosPartidoId').value || '{{ $partido->id }}';

        fetch(`/admin/partidos/${partidoId}/eventos/agregar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    eventos: eventos
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    actualizarListaEventos();
                    console.log('Eventos guardados en base de datos');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron guardar los eventos.'
                    });
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión.'
                });
            });
    }

    // Si quieres precargar eventos existentes:
    document.addEventListener('DOMContentLoaded', function() {
        eventos = <?= json_encode($eventosArray) ?> || [];
        actualizarListaEventos();
    });
</script>
@endpush