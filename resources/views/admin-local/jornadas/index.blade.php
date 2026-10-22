@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-zinc-100 tracking-tight">Calendario de Jornadas</h1>
            <p class="text-zinc-400">Visualiza y administra las jornadas y sus enfrentamientos.</p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.local.jornadas.index') }}" method="GET" class="flex items-center gap-2">
                <select name="torneo_id" onchange="this.form.submit()" class="bg-zinc-950 border border-zinc-700 text-zinc-100 rounded-xl text-sm px-3 py-2.5 focus:ring-lime-400 focus:border-lime-400 shadow-sm">
                    <option value="">🌐 Todos los Torneos</option>
                    @foreach($torneos as $t)
                        <option value="{{ $t->id }}" {{ $torneoId == $t->id ? 'selected' : '' }}>🏆 {{ $t->nombre }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.local.jornadas.create') }}" class="bg-lime-400 text-zinc-950 font-semibold px-4 py-2.5 rounded-xl hover:bg-lime-300 transition-colors flex items-center gap-2 text-sm">
                <i class="bi bi-plus-lg"></i> Nueva Jornada
            </a>
        </div>
    </div>

    <!-- Timeline Grid de Jornadas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($jornadas as $jornada)
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-3 py-1 rounded-xl bg-lime-400/10 text-lime-400 text-xs font-bold">Jornada {{ $jornada->orden }}</span>
                        <span class="text-xs text-zinc-500">{{ $jornada->torneo->nombre ?? '' }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-zinc-100 mb-2">{{ $jornada->nombre }}</h3>
                    <p class="text-xs text-zinc-400 mb-4">
                        <i class="bi bi-calendar mr-1"></i>
                        {{ $jornada->fecha_inicio?->format('d/m/Y') ?? 'Sin inicio' }} - {{ $jornada->fecha_fin?->format('d/m/Y') ?? 'Sin fin' }}
                    </p>

                    <!-- Partidos de la jornada -->
                    <div class="space-y-2 mb-6">
                        @forelse($jornada->partidos as $partido)
                            <div class="bg-zinc-950 border border-zinc-800 rounded-xl p-3 flex items-center justify-between text-xs">
                                <span class="text-zinc-300 font-medium truncate w-24">{{ $partido->equipoLocal->nombre }}</span>
                                <span class="font-bold text-zinc-100">
                                    {{ $partido->estado === 'jugado' ? $partido->goles_local . '-' . $partido->goles_visitante : 'VS' }}
                                </span>
                                <span class="text-zinc-300 font-medium truncate w-24 text-right">{{ $partido->equipoVisitante->nombre }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-zinc-500 italic text-center py-2">No hay partidos en esta jornada</p>
                        @endforelse
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-zinc-800">
                    <a href="{{ route('admin.local.jornadas.edit', $jornada) }}" class="text-xs font-semibold text-blue-400 hover:text-blue-300">Editar Jornada</a>
                    <button type="button" onclick="eliminarJornada({{ $jornada->id }})" class="text-xs font-semibold text-red-400 hover:text-red-300">Eliminar</button>
                    <form id="delete-form-{{ $jornada->id }}" action="{{ route('admin.local.jornadas.destroy', $jornada) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full border-dashed border-2 border-zinc-800 rounded-2xl py-16 flex flex-col items-center justify-center text-center">
                <i class="bi bi-calendar-x text-5xl text-zinc-700 mb-4"></i>
                <p class="text-zinc-400 font-medium text-lg">No hay jornadas registradas</p>
                <a href="{{ route('admin.local.jornadas.create') }}" class="bg-lime-400 text-zinc-950 font-semibold px-6 py-3 rounded-xl mt-6 hover:bg-lime-300 transition-colors">
                    Crear Primera Jornada
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $jornadas->links() }}
    </div>
</div>

<script>
    function eliminarJornada(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esta acción!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#27272a',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            background: '#18181b',
            color: '#f4f4f5'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endsection
