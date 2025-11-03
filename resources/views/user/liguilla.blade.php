@extends('user.layouts.app')

@section('title', 'Liguilla: ' . $liguilla->nombre)

@section('content')
<div class="container py-4 mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">{{ $liguilla->nombre }}</h1>
            <small class="text-muted">Torneo: {{ $liguilla->torneo->nombre ?? '-' }}</small>
        </div>
        <div>
            <span class="badge bg-primary">Código: {{ $liguilla->codigo_unico }}</span>
        </div>
    </div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-3" id="liguillaTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="alineacion-tab" data-bs-toggle="tab" data-bs-target="#alineacion" type="button" role="tab">Alineación</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="plantilla-tab" data-bs-toggle="tab" data-bs-target="#plantilla" type="button" role="tab">Plantilla</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="clasificacion-tab" data-bs-toggle="tab" data-bs-target="#clasificacion" type="button" role="tab">Clasificación</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="jornadas-tab" data-bs-toggle="tab" data-bs-target="#jornadas" type="button" role="tab">Jornadas / Resultados</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="participantes-tab" data-bs-toggle="tab" data-bs-target="#participantes" type="button" role="tab">Participantes</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="historico-tab" data-bs-toggle="tab" data-bs-target="#historico" type="button" role="tab">Histórico</button>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Alineacion -->
        <div class="tab-pane fade show active" id="alineacion" role="tabpanel" aria-labelledby="alineacion-tab">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Alineación para la jornada</h5>
                    <p class="text-muted">Selecciona hasta {{ $liguilla->torneo->jugadores_por_equipo }} jugadores de tu plantilla.</p>

                    <!-- Selector de jornada -->
                    <form id="formSeleccionJornada" class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="jornada_id" class="form-label">Jornada</label>
                            <select id="jornada_id" name="jornada_id" class="form-select">
                                <option value="">Selecciona jornada</option>
                                @foreach($liguilla->torneo->jornadas()->orderBy('orden')->get() as $j)
                                <option value="{{ $j->id }}"
                                    {{ $jornadaActiva && $jornadaActiva->id === $j->id ? 'selected' : '' }}>
                                    J {{ $j->orden }} - {{ $j->nombre ?? '' }}
                                </option>
                                @endforeach
                            </select>

                        </div>
                    </form>

                    <!-- Campo de fútbol -->
                    <div class="futbol-campo mb-4 position-relative">
                        <div class="alineacion-slots d-flex flex-wrap justify-content-center gap-3">
                            @for($i = 1; $i <= $liguilla->torneo->jugadores_por_equipo; $i++)
                                <div class="slot card text-center d-flex align-items-center justify-content-center" data-slot="{{ $i }}">
                                    <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                                        <i class="bi bi-plus-circle-fill text-white fs-3 slot-plus" style="cursor: pointer;"></i>
                                        <small class="text-white mt-1">Vacío</small>
                                    </div>
                                </div>
                                @endfor
                        </div>
                        <!-- Overlay loader -->
                        <div id="loaderOverlay" class="loader-overlay" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario alineación -->
                    <form id="formAlineacion" method="POST"
                        action="{{ url('/user/liguillas/'.$liguilla->id.'/alineacion/guardar') }}"
                        class="mt-4">
                        @csrf
                        <input type="hidden" name="jornada_id" id="form_jornada_id" value="{{ $jornadaActiva->id ?? '' }}">
                        <div id="alineacionInputs"></div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" {{ $bloqueada ? 'disabled' : '' }}>
                                Guardar alineación
                            </button>
                            <button type="button" class="btn btn-secondary">Cancelar</button>
                        </div>
                    </form>

                    @if($bloqueada)
                    <div class="alert alert-warning mt-3">
                        ⚠️ La jornada ya ha comenzado, no puedes modificar la alineación.
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Modal Plantilla -->
        <div class="modal fade" id="modalSeleccionJugador" tabindex="-1" aria-labelledby="modalSeleccionJugadorLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalSeleccionJugadorLabel">Selecciona jugador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row row-cols-2 row-cols-md-4 g-3">
                            @foreach($miPlantilla as $jugador)
                            <div class="col">
                                <div class="card jugador-card selectable" data-jugador-id="{{ $jugador->id }}">
                                    <div class="card-body text-center p-2">
                                        <div class="jugador-avatar mb-2">
                                            <img src="{{ $jugador->equipoEnTorneo($liguilla->torneo_id)->logo ? asset($jugador->equipoEnTorneo($liguilla->torneo_id)->logo) : asset('assets/media/images/default-team.png') }}" alt="logo equipo">
                                        </div>
                                        <img src="{{ $jugador->foto ? asset($jugador->foto) : asset('assets/media/images/default-player.png') }}"
                                            alt="{{ $jugador->nombre }} {{ $jugador->apellido1 }}"
                                            width="60" class="rounded-circle mb-2">
                                        <h6 class="mb-0">{{ $jugador->nombre }} {{ $jugador->apellido1 }}</h6>
                                        <small class="text-muted">{{ $jugador->posicion ?? 'Jugador' }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnVaciarSlot" class="btn btn-outline-danger">Vaciar posición</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plantilla -->
        <div class="tab-pane fade" id="plantilla" role="tabpanel" aria-labelledby="plantilla-tab">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Tu Plantilla</h5>
                    <div class="row row-cols-2 row-cols-md-4 g-3">
                        @foreach($miPlantilla as $jugador)
                        <div class="col">
                            <div class="card jugador-card" data-jugador-id="{{ $jugador->id }}">
                                <div class="card-body text-center p-2">
                                    <div class="jugador-avatar mb-2">
                                        <img src="{{ $jugador->equipoEnTorneo($liguilla->torneo_id)->logo ? asset($jugador->equipoEnTorneo($liguilla->torneo_id)->logo) : asset('assets/media/images/default-team.png') }}"
                                            alt="{{ $jugador->equipoEnTorneo($liguilla->torneo_id)->nombre }}">
                                    </div>
                                    <img src="{{ $jugador->foto ? asset($jugador->foto) : asset('assets/media/images/default-player.png') }}"
                                        alt="{{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}" width="80" class="rounded-circle mb-2">
                                    <h4 class="mb-0">{{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}</h4>
                                    <h6 class="text-muted mb-0">{{ $jugador->equipoEnTorneo($liguilla->torneo_id)->nombre }}</h6>
                                    <small class="text-muted">{{ $jugador->posicion ?? 'Jugador' }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Jugador -->
        <div class="modal fade" id="modalJugador" tabindex="-1" aria-labelledby="modalJugadorLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalJugadorLabel">Información del Jugador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Nombre y foto -->
                        <div class="text-center mb-4">
                            <img id="modalJugadorFoto" src="" alt="Foto jugador" class="rounded-circle mb-3" width="120">
                            <h2 id="modalJugadorNombre" class="fw-bold"></h2>
                        </div>

                        <!-- Estadísticas distribuidas en columnas -->
                        <div class="row text-center">
                            <!-- Columna 1 -->
                            <div class="col-4 mb-3">
                                <p><strong>Equipo:</strong> <span id="modalJugadorEquipo"></span></p>
                                <p><strong>Posición:</strong> <span id="modalJugadorPosicion"></span></p>
                                <p><strong>Edad:</strong> <span id="modalJugadorEdad"></span></p>
                                <p><strong>Partidos:</strong> <span id="modalJugadorPartidos"></span></p>
                            </div>

                            <!-- Columna 2 -->
                            <div class="col-4 mb-3">
                                <p><strong>Goles:</strong> <span id="modalJugadorGoles"></span></p>
                                <p><strong>Asistencias:</strong> <span id="modalJugadorAsistencias"></span></p>
                                <p><strong>Paradas:</strong> <span id="modalJugadorParadas"></span></p>
                            </div>

                            <!-- Columna 3 -->
                            <div class="col-4 mb-3">
                                <p><strong>Amarillas:</strong> <span id="modalJugadorAmarillas"></span></p>
                                <p><strong>Rojas:</strong> <span id="modalJugadorRojas"></span></p>
                                <p><strong>Faltas:</strong> <span id="modalJugadorFaltas"></span></p>
                            </div>
                        </div>

                        <!-- Puntos al final -->
                        <div class="text-center mt-3">
                            <h4><strong>Puntos:</strong> <span id="modalJugadorPuntos"></span></h4>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>



        <!-- Clasificación -->
        <div class="tab-pane fade" id="clasificacion" role="tabpanel" aria-labelledby="clasificacion-tab">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Clasificación general</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Usuario</th>
                                    <th>Equipo</th>
                                    <th class="text-end">Puntos</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Asumimos que $clasificacion es pasado desde el controlador ordenado por puntos --}}
                                @foreach($clasificacion ?? $liguilla->usuarios()->withPivot('puntos','posicion')->get() as $u)
                                <tr>
                                    <td>{{ $u->pivot->posicion ?? '-' }}</td>
                                    <td>{{ $u->nombre ?? $u->email ?? 'Usuario' }}</td>
                                    <td>{{ $u->pivot->nombre_equipo ?? '-' }}</td>
                                    <td class="text-end">{{ $u->pivot->puntos ?? 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jornadas / Resultados -->
        <div class="tab-pane fade" id="jornadas" role="tabpanel" aria-labelledby="jornadas-tab">
            <div class="row">
                <div class="col-md-4">
                    <div class="list-group mb-3">
                        @foreach($liguilla->torneo->jornadas()->orderBy('orden')->get() as $j)
                        <a href="#" class="list-group-item list-group-item-action jornada-link" data-jornada-id="{{ $j->id }}">
                            Jornada {{ $j->orden }} <br>
                            <small class="text-muted">{{ $j->nombre }}</small>
                        </a>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-8">
                    <div id="panelJornadaSeleccionada">
                        <h5>Selecciona una jornada a la izquierda</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Participantes -->
        <div class="tab-pane fade" id="participantes" role="tabpanel" aria-labelledby="participantes-tab">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Participantes</h5>
                    <div class="list-group">
                        @foreach($liguilla->plantillas()->with('usuario')->get() as $plantilla)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $plantilla->usuario->nombreUsuario ?? 'Usuario' }}</strong><br>
                                <small class="text-muted">Plantilla: {{ $plantilla->jugadores()->count() }} jugadores</small>
                            </div>
                            <div>
                                <a href="{{ url('/user/perfil/'.$plantilla->usuario->id) }}" class="btn btn-sm btn-secondary me-2">Ver perfil</a>
                                <a href="{{ url('/user/liguillas/'.$liguilla->id.'/usuario/'.$plantilla->usuario->id) }}" class="btn btn-sm btn-primary">Detalle</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Histórico / Detalles -->
        <div class="tab-pane fade" id="historico" role="tabpanel" aria-labelledby="historico-tab">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Histórico de alineaciones y puntuaciones</h5>
                    <p class="text-muted">Selecciona un participante para ver sus alineaciones por jornada.</p>

                    <div class="row">
                        <div class="col-md-4">
                            <select id="selectUsuarioHistorico" class="form-select">
                                <option value="">Selecciona participante</option>
                                @foreach($liguilla->plantillas()->with('usuario')->get() as $p)
                                <option value="{{ $p->usuario->id }}">{{ $p->usuario->nombreUsuario }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8" id="panelHistoricoUsuario">
                            <p class="text-muted">No hay participante seleccionado.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Pequeños ajustes de estilo */
    .card .card-body {
        padding: 1.25rem;
    }

    .alineacion-check {
        transform: scale(1.05);
    }

    .jornada-link {
        cursor: pointer;
    }

    .futbol-campo {
        background: linear-gradient(#2e7d32 50%, #1b5e20 50%);
        background-size: 100% 40px;
        border: 2px solid #fff;
        border-radius: 10px;
        padding: 20px;
        color: white;
        min-height: 250px;
    }

    .slot {
        width: 100px;
        height: 120px;
        background: rgba(255, 255, 255, 0.1);
        border: 2px dashed #ffffff;
        border-radius: 8px;
    }

    .slot:hover {
        border-color: #0d6efd;
        /* azul Bootstrap al pasar ratón */
        cursor: pointer;
    }

    .slot.ocupado {
        border: 2px solid #ffffff;
        /* verde */
    }

    .slot.vacio {
        border: 2px dashed #ffffff;
        /* gris */
    }

    .jugador-card {
        cursor: pointer;
        transition: transform .2s;
    }

    .jugador-card:hover {
        transform: scale(1.05);
    }

    .jugador-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        line-height: 40px;
    }

    .jugador-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        /* opcional, redondeado */
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: #f0f0f0;
        /* color de fondo genérico */
        font-size: 24px;
        /* tamaño del ⚽ */
    }

    .jugador-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* para que el logo no se deforme */
    }

    .loader-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        /* oscurece el área */
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        /* por encima de los slots */
    }
</style>
@endpush

@push('scripts')
<script>
    let slotSeleccionado = null;

    // Abrir modal al pulsar el slot
    document.querySelectorAll('.slot').forEach(slot => {
        slot.addEventListener('click', function() {
            slotSeleccionado = this;
            actualizarJugadoresDisponibles(); // refrescar para ocultar ocupados
            const modal = new bootstrap.Modal(document.getElementById('modalSeleccionJugador'));
            modal.show();
        });
    });

    // Seleccionar jugador en alineación modal
    document.querySelectorAll('.jugador-card.selectable').forEach(jugador => {
        jugador.addEventListener('click', function() {
            if (!slotSeleccionado) return;

            const jugadorId = this.dataset.jugadorId;
            const nombre = this.querySelector('h6').textContent;
            const foto = this.querySelector('img.rounded-circle').src;

            // Evitar duplicados: comprobar si ya está en inputs
            if (document.querySelector(`#alineacionInputs input[value="${jugadorId}"]`)) {
                alert("Ese jugador ya está en la alineación.");
                return;
            }

            // Rellenar el slot
            const body = slotSeleccionado.querySelector('.card-body');
            body.innerHTML = '';
            const img = document.createElement('img');
            img.src = foto;
            img.width = 50;
            img.classList.add('rounded-circle', 'mb-1');
            body.appendChild(img);
            // Marcar el slot como ocupado
            slotSeleccionado.classList.add('ocupado');
            slotSeleccionado.classList.remove('vacio');


            const nombreEl = document.createElement('small');
            nombreEl.textContent = nombre;
            nombreEl.classList.add('text-white');
            body.appendChild(nombreEl);

            // Añadir input hidden
            let slotNumber = slotSeleccionado.dataset.slot;
            let formInput = document.querySelector(`#alineacionInputs input[data-slot="${slotNumber}"]`);
            if (!formInput) {
                formInput = document.createElement('input');
                formInput.type = 'hidden';
                formInput.name = 'jugadores[]';
                formInput.dataset.slot = slotNumber;
                document.getElementById('alineacionInputs').appendChild(formInput);
            }
            formInput.value = jugadorId;

            bootstrap.Modal.getInstance(document.getElementById('modalSeleccionJugador')).hide();
        });
    });

    //Seleccionar jugador en plantilla modal
    document.querySelectorAll('#plantilla .jugador-card').forEach(card => {
        card.addEventListener('click', function() {
            const idJugador = this.dataset.jugadorId;
            const idTorneo = "{{ $liguilla->torneo_id }}";
            // Aquí puedes hacer un fetch para traer info completa desde Laravel
            fetch(`/user/jugadores/${idJugador}/info/torneo/${idTorneo}`)
                .then(res => res.json())
                .then(data => {
                    // Rellenar modal
                    document.getElementById('modalJugadorFoto').src = data.foto || '/assets/media/images/default-player.png';
                    document.getElementById('modalJugadorNombre').textContent = `${data.nombre} ${data.apellido1} ${data.apellido2}`;
                    document.getElementById('modalJugadorEquipo').textContent = data.equipo;
                    document.getElementById('modalJugadorPosicion').textContent = data.posicion || 'Jugador';
                    document.getElementById('modalJugadorEdad').textContent = data.edad;
                    document.getElementById('modalJugadorPartidos').textContent = data.partidos;
                    document.getElementById('modalJugadorGoles').textContent = data.goles;
                    document.getElementById('modalJugadorAsistencias').textContent = data.asistencias;
                    document.getElementById('modalJugadorParadas').textContent = data.paradas;
                    document.getElementById('modalJugadorFaltas').textContent = data.faltas;
                    document.getElementById('modalJugadorAmarillas').textContent = data.tarjetas_amarillas;
                    document.getElementById('modalJugadorRojas').textContent = data.tarjetas_rojas;
                    document.getElementById('modalJugadorPuntos').textContent = data.puntos;

                    // Mostrar modal
                    const modal = new bootstrap.Modal(document.getElementById('modalJugador'));
                    modal.show();
                })
                .catch(err => {
                    console.error(err);
                    alert('No se pudo cargar la información del jugador.');
                });
        });
    });

    // Botón vaciar slot
    document.getElementById('btnVaciarSlot').addEventListener('click', function() {
        if (!slotSeleccionado) return;

        const body = slotSeleccionado.querySelector('.card-body');
        body.innerHTML = '<i class="bi bi-plus-circle-fill text-white fs-3 slot-plus" style="cursor: pointer;"></i><small class="text-white mt-1">Vacío</small>';

        let slotNumber = slotSeleccionado.dataset.slot;
        const input = document.querySelector(`#alineacionInputs input[data-slot="${slotNumber}"]`);
        if (input) input.remove();

        bootstrap.Modal.getInstance(document.getElementById('modalSeleccionJugador')).hide();
        // Marcar el slot como vacío
        slotSeleccionado.classList.add('vacio');
        slotSeleccionado.classList.remove('ocupado');
    });

    // Al cambiar la jornada en el select
    document.getElementById('jornada_id').addEventListener('change', function() {
        const jornadaId = this.value;
        const liguillaId = "{{$liguilla->id}}";
        const loader = document.getElementById('loaderOverlay');

        document.getElementById('form_jornada_id').value = jornadaId;
        if (!jornadaId) return;

        // Mostrar overlay
        loader.style.display = 'flex';

        fetch(`/user/liguillas/${liguillaId}/alineacion/${jornadaId}`)
            .then(res => res.json())
            .then(data => {
                // Limpiar y rellenar slots...
                document.querySelectorAll('.slot').forEach((slot, index) => {
                    const body = slot.querySelector('.card-body');
                    if (data.status === 'ok' && data.jugadores[index]) {
                        const jugador = data.jugadores[index];
                        body.innerHTML = `
                        <img src="${jugador.foto}" width="50" class="rounded-circle mb-1">
                        <small class="text-white">${jugador.nombre} ${jugador.apellido1} ${jugador.apellido2}</small>
                    `;
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'jugadores[]';
                        input.dataset.slot = index + 1;
                        input.value = jugador.id;
                        document.getElementById('alineacionInputs').appendChild(input);
                        slot.classList.add('ocupado');
                        slot.classList.remove('vacio');
                    } else {
                        body.innerHTML = '<i class="bi bi-plus-circle-fill text-white fs-3 slot-plus" style="cursor: pointer;"></i><small class="text-white mt-1">Vacío</small>';
                        slot.classList.add('vacio');
                        slot.classList.remove('ocupado');
                    }
                });

                actualizarJugadoresDisponibles();
                loader.style.display = 'none'; // ocultar overlay
            })
            .catch(err => {
                console.error(err);
                loader.style.display = 'none';
            });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('jornada_id');
        if (select.value) {
            // dispara el evento change para que pinte la alineación inicial
            select.dispatchEvent(new Event('change'));
        }
    });

    //Submit
    document.getElementById('formAlineacion').addEventListener('submit', function(e) {
        e.preventDefault(); // evitar recarga

        let form = e.target;
        let formData = new FormData(form);

        fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json' // para que Laravel devuelva JSON
                },
                body: formData
            })
            .then(async response => {
                if (!response.ok) {
                    // error de validación u otro
                    let errorData = await response.json();
                    throw errorData;
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: data.message || 'La alineación se guardó correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                let message = error.message || 'Ocurrió un error al guardar la alineación';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            });
    });

    // Ocultar jugadores ya ocupados
    function actualizarJugadoresDisponibles() {
        const usados = Array.from(document.querySelectorAll('#alineacionInputs input')).map(i => i.value);
        document.querySelectorAll('.jugador-card.selectable').forEach(card => {
            if (usados.includes(card.dataset.jugadorId)) {
                card.classList.add('opacity-50', 'pe-none'); // desactiva
            } else {
                card.classList.remove('opacity-50', 'pe-none');
            }
        });
    }
</script>
@endpush