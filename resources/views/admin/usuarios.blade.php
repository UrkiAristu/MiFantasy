@extends('admin.layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 text-zinc-200">
    <!-- Mensajes -->
    @if ($errors->any())
    <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl text-sm">
        @foreach ($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    @if(session('success'))
    <div class="mb-6 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-zinc-100">Usuarios Registrados</h1>
    </div>

    <!-- Tabla Container -->
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm p-6">
        <div class="overflow-x-auto">
            <table id="tablaUsuarios" class="w-full text-left text-sm text-zinc-300">
                <thead class="bg-zinc-950/80 text-zinc-400 uppercase text-xs tracking-wider border-b border-zinc-800">
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
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($usuarios as $usuario)
                    <tr class="hover:bg-zinc-800/30 transition-colors">
                        <td class="py-3 px-4 text-center font-mono text-zinc-400">{{ $usuario->id }}</td>
                        <td class="py-3 px-4 text-center font-bold text-zinc-100">{{ $usuario->name }}</td>
                        <td class="py-3 px-4 text-center text-zinc-400">{{ $usuario->email }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($usuario->admin)
                            <span class="inline-block px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">Sí</span>
                            @else
                            <span class="inline-block px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-400 text-xs font-semibold">No</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center text-zinc-300 text-xs">{{ $usuario->created_at->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($usuario->active)
                            <span class="inline-block px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">Sí</span>
                            @else
                            <span class="inline-block px-2 py-0.5 rounded-full bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-semibold">No</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center space-x-1">
                            <a href="{{ url('/admin/usuarios/'.$usuario->id) }}" class="inline-flex items-center gap-1 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors" title="Ver usuario">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <form action="{{ url('/admin/usuarios/'.$usuario->id.'/toggle') }}" method="POST" class="inline-block form-toggle-usuario">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                    class="inline-flex items-center gap-1 {{ $usuario->active ? 'bg-red-500/10 hover:bg-red-500/20 text-red-400' : 'bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400' }} px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors cursor-pointer"
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
                    background: '#18181b',
                    color: '#f4f4f5',
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
