@extends('admin.layouts.app')

@section('title', 'Usuarios')

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
                    <td class="text-center">{{ $usuario->name }}</td>
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
                        @if($usuario->active)
                        <span class="badge bg-success">Sí</span>
                        @else
                        <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ url('/admin/usuarios/'.$usuario->id) }}" class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i> Ver</a>
                            <form action="{{ url('/admin/usuarios/'.$usuario->id.'/toggle') }}" method="POST" class="d-inline form-toggle-usuario">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                        class="btn btn-sm {{ $usuario->active ? 'btn-danger' : 'btn-success' }}"
                                        data-nombre="{{ $usuario->name }}"
                                        data-estado="{{ $usuario->active ? 'inhabilitar' : 'habilitar' }}">
                                    <i class="bi {{ $usuario->active ? 'bi-x-circle' : 'bi-check-circle' }}"></i>
                                    {{ $usuario->active ? 'Inhabilitar' : 'Habilitar' }}
                                </button>
                            </form>
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
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('.form-toggle-usuario');

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // Evita envío inmediato

            const button = form.querySelector('button[type="submit"]');
            const nombre = button.dataset.nombre;
            const estado = button.dataset.estado;

            Swal.fire({
                title: `¿Seguro que deseas ${estado} a ${nombre}?`,
                text: estado === 'inhabilitar' 
                      ? 'El usuario no podrá acceder al sistema.' 
                      : 'El usuario podrá volver a acceder.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Sí, ${estado}`,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Envía el formulario si confirma
                }
            });
        });
    });
});
</script>
@endpush