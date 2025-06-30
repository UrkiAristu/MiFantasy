@extends('admin.layouts.app')

@section('title', 'Usuarios Registrados')

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
    <h1 class="mb-4">Usuarios Registrados</h1>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table id="tablaUsuarios" class="table table-striped table-hover align-middle mt-5 text-center">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Nombre de Usuario</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Admin</th>
                    <th class="text-center">Fecha de Registro</th>
                    <th class="text-center">Activo</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                <tr>
                    <td class="text-center">{{ $usuario->id }}</td>
                    <td class="text-center">{{ $usuario->nombreUsuario }}</td>
                    <td class="text-center">{{ $usuario->email }}</td>
                    <td class="text-center">
                        @if($usuario->admin)
                        <span class="badge bg-success">Sí</span>
                        @else
                        <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $usuario->created_at->format('d/m/Y') }}</td>
                    <td class="text-center">
                        @if($usuario->activo)
                        <span class="badge bg-success">Sí</span>
                        @else
                        <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ url('/admin/usuarios/'.$usuario->id) }}" class="btn btn-sm btn-outline-info">Ver</a>
                        @if($usuario->activo)
                        <a href="{{ url('/admin/usuarios/'.$usuario->id.'/inhabilitar') }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro de inhabilitar este usuario?')">Inhabilitar</a>
                        @else
                        <a href="{{ url('/admin/usuarios/'.$usuario->id.'/habilitar') }}" class="btn btn-sm btn-outline-success" onclick="return confirm('¿Estás seguro de habilitar este usuario?')">Habilitar</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No hay usuarios registrados.</td>
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
        $('#tablaUsuarios').DataTable({
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