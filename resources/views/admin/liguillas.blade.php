@extends('admin.layouts.app')

@section('title', 'Liguillas')

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
        <h1 class="text-2xl font-extrabold tracking-tight text-zinc-100">Liguillas</h1>
    </div>

    <!-- Tabla Container -->
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm p-6">
        <div class="overflow-x-auto">
            <table id="tablaLiguillas" class="w-full text-left text-sm text-zinc-300">
                <thead class="bg-zinc-950/80 text-zinc-400 uppercase text-xs tracking-wider border-b border-zinc-800">
                    <tr>
                        <th class="py-3 px-4 text-center">ID</th>
                        <th class="py-3 px-4 text-center">Nombre</th>
                        <th class="py-3 px-4 text-center">Torneo</th>
                        <th class="py-3 px-4 text-center">Creador</th>
                        <th class="py-3 px-4 text-center">Nº Participantes</th>
                        <th class="py-3 px-4 text-center">Fecha de Creación</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($liguillas as $liguilla)
                    <tr class="hover:bg-zinc-800/30 transition-colors">
                        <td class="py-3 px-4 text-center font-mono text-zinc-400">{{ $liguilla->id }}</td>
                        <td class="py-3 px-4 text-center font-bold text-zinc-100">{{ $liguilla->nombre }}</td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex flex-col items-center gap-2">
                                @if(!empty($liguilla->torneo->logo))
                                <img src="{{ asset($liguilla->torneo->logo) }}" alt="Logo" class="w-10 h-10 object-contain rounded-lg bg-zinc-950 p-1 border border-zinc-800">
                                @endif
                                <span class="text-zinc-300">{{ $liguilla->torneo->nombre }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center text-zinc-300">{{ $liguilla->creador->name ?? 'Desconocido' }}</td>
                        <td class="py-3 px-4 text-center text-zinc-300">{{ $liguilla->usuarios()->count() }} / {{ $liguilla->max_usuarios }}</td>
                        <td class="py-3 px-4 text-center text-zinc-400 text-xs">{{ $liguilla->created_at }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-zinc-500">No hay liguillas creadas.</td>
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
        $('#tablaLiguillas').DataTable({
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
</script>
@endpush
