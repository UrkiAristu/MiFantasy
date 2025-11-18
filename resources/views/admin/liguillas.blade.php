@extends('admin.layouts.app')

@section('title', 'Liguillas')

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
        <h1 class="mb-0">Liguillas</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table id="tablaLiguillas" class="table table-striped table-hover align-middle mt-5 text-center">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Torneo</th>
                    <th class="text-center">Creador</th>
                    <th class="text-center">Nº Participantes</th>
                    <th class="text-center">Fecha de Creación</th>
                </tr>
            </thead>
            <tbody>
                @forelse($liguillas as $liguilla)
                <tr>
                    <td class="text-center">{{ $liguilla->id }}</td>
                    <td class="text-center">{{ $liguilla->nombre }}</td>
                    <td class="text-center">
                        <div class="d-flex flex-column align-items-center">
                            @if(!empty($liguilla->torneo->logo))
                                <img src="{{ asset($liguilla->torneo->logo) }}" alt="Logo" style="width:50px; height:50px; object-fit:contain;" class="mb-2">
                            @endif
                            <div>{{ $liguilla->torneo->nombre }}</div>
                        </div>
                    </td>
                    <td class="text-center">{{ $liguilla->creador->name ?? 'Desconocido' }}</td>
                    <td class="text-center">{{ $liguilla->usuarios()->count() }} / {{ $liguilla->max_usuarios }}</td>
                    <td class="text-center">{{ $liguilla->created_at }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No hay liguillas creadas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('#tablaLiguillas').DataTable({
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