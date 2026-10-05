@extends('admin.layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 text-zinc-900">
    <!-- Mensajes -->
    @if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl text-sm">
        @foreach ($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    @if(session('success'))
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-zinc-900">Usuarios Registrados</h1>
    </div>

    <!-- Tabla Container -->
    <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6">
        <div class="overflow-x-auto">
            <table id="tablaUsuarios" class="w-full text-left text-sm text-zinc-700">
                <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3 px-4 text-center">ID</th>
                        <th class="py-3 px-4 text-center">Nombre de Usuario</th>
                        <th class="py-3 px-4 text-center">Email</th>
                        <th class="py-3 px-4 text-center">Admin</th>
                        <th class="py-3 px-4 text-center">Fecha de Registro</th>
                        <th class="py-3 px-4 text-center">Activo</th>
                        <th class="py-3 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($usuarios as $usuario)
                    <tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="py-3 px-4 text-center font-mono text-zinc-500">{{ $usuario->id }}</td>
                        <td class="py-3 px-4 text-center font-bold text-zinc-900">{{ $usuario->name }}</td>
                        <td class="py-3 px-4 text-center text-zinc-600">{{ $usuario->email }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($usuario->admin)
                            <span class="inline-block px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">Sí</span>
                            @else
                            <span class="inline-block px-2 py-0.5 rounded-full bg-zinc-100 text-zinc-600 text-xs font-semibold">No</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center text-zinc-600 text-xs">{{ $usuario->created_at->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($usuario->active)
                            <span class="inline-block px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">Sí</span>
                            @else
                            <span class="inline-block px-2 py-0.5 rounded-full bg-red-50 border border-red-200 text-red-700 text-xs font-semibold">No</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center space-x-1">
                            <a href="{{ url('/admin/usuarios/'.$usuario->id) }}" class="inline-flex items-center gap-1 bg-cyan-50 hover:bg-cyan-100 text-cyan-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors" title="Ver usuario">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <form action="{{ url('/admin/usuarios/'.$usuario->id.'/toggle') }}" method="POST" class="inline-block form-toggle-usuario">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                    class="inline-flex items-center gap-1 {{ $usuario->active ? 'bg-red-50 hover:bg-red-100 text-red-700' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700' }} px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors cursor-pointer"
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
                        <td colspan="7" class="py-8 text-center text-zinc-500">No hay usuarios registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#tablaUsuarios').DataTable({
            destroy: true,
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

    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.form-toggle-usuario');

        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const button = form.querySelector('button[type="submit"]');
                const nombre = button.dataset.nombre;
                const estado = button.dataset.estado;

                Swal.fire({
                    title: `¿Seguro que deseas ${estado} a ${nombre}?`,
                    text: estado === 'inhabilitar' ?
                        'El usuario no podrá acceder al sistema.' : 'El usuario podrá volver a acceder.',
                    icon: 'warning',
                    background: '#ffffff',
                    color: '#18181b',
                    showCancelButton: true,
                    confirmButtonColor: '#a3e635',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: `Sí, ${estado}`,
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
