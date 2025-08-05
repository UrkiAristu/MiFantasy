@extends('user.layouts.app')

@section('title', 'Torneos Disponibles')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Torneos Activos</h1>
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
    <div class="row g-4">
        @foreach($torneos as $torneo)
        <div class="col-md-3 mb-4">
            <div class="card text-white shadow"
                style="background: url('{{ asset($torneo->logo) }}') ;
                background-size: contain;
                background-repeat: no-repeat;
                background-position: center;
                height: 250px;
                cursor: pointer;
                border: 3px solid #000;
                border-radius: 1rem;
                overflow: hidden;"
                data-bs-toggle="modal"
                data-bs-target="#modalLiguilla"
                data-nombre="{{ $torneo->nombre }}"
                data-id="{{ $torneo->id }}"
                data-descripcion="{{ $torneo->descripcion }}">
                @if(is_null($torneo->logo))
                <div class="card-body d-flex flex-column justify-content-start align-items-center bg-dark bg-opacity-50 p-3 h-100">
                    <h2 class="card-title mb-2 text-center mt-5">{{ $torneo->nombre }}</h4>
                        <p class="card-text text-center">{{ $torneo->descripcion }}</p>
                </div>
                @endif
            </div>
        </div>
        @endforeach


        <!--Modal único -->
        <div class="modal fade" id="modalLiguilla" tabindex="-1" aria-labelledby="modalLiguillaLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ url('/user/liguillas/crear') }}">
                        @csrf
                        <input type="hidden" name="torneo_id" id="modal_torneo_id">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLiguillaLabel">Configurar Liguilla</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p id="modalLiguillaDescripcion" class="mb-3 text-muted"></p>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="num_max_part" class="form-label">Número máximo de particiantes</label>
                                <input type="number" name="num_max_part" id="num_max_part" class="form-control" min="0" value="10" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Crear Liguilla</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    var modalLiguilla = document.getElementById('modalLiguilla');
    modalLiguilla.addEventListener('show.bs.modal', function(event) {
        // El botón o elemento que disparó el modal
        var trigger = event.relatedTarget;

        // Obtener datos del card
        var torneoId = trigger.getAttribute('data-id');
        var torneoNombre = trigger.getAttribute('data-nombre');

        // Rellenar los campos del modal
        var inputTorneoId = modalLiguilla.querySelector('#modal_torneo_id');
        var inputNombre = modalLiguilla.querySelector('#nombre');
        var tituloModal = modalLiguilla.querySelector('.modal-title');

        inputTorneoId.value = torneoId;
        tituloModal.textContent = 'Crear Liguilla de ' + torneoNombre;

        // Opcional: limpiar el input nombre para que el usuario escriba uno nuevo
        inputNombre.value = '';
    });
</script>
@endpush