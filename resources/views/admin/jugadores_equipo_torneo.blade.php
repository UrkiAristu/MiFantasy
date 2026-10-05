@extends('admin.layouts.app')

@section('title', 'Plantilla de Equipo en Torneo')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 text-zinc-900">
    @if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl text-sm">
        <strong class="font-bold">Se encontraron errores:</strong>
        <ul class="mt-1 list-disc list-inside">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Logo Equipo -->
        <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm p-6 text-center">
            <a href="{{ url('/admin/equipos/'.$equipo->id) }}" class="group inline-block">
                @if($equipo->logo)
                <img src="{{ asset($equipo->logo) }}" alt="Logo Equipo" class="w-24 h-24 object-contain mx-auto rounded-xl bg-white p-2 border border-zinc-200 mb-3" onerror="this.outerHTML='<div class=\'w-24 h-24 mx-auto rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400 text-xs font-bold border border-zinc-200 mb-3\'>N/A</div>'">
                @else
                <div class="w-24 h-24 mx-auto rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400 text-xs font-bold border border-zinc-200 mb-3">Sin logo</div>
                @endif
                <h3 class="text-lg font-bold text-zinc-900 group-hover:text-lime-600 transition-colors">{{ $equipo->nombre }}</h3>
            </a>
        </div>

        <!-- Logo Torneo -->
        <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm p-6 text-center">
            <a href="{{ url('/admin/torneos/'.$torneo->id) }}" class="group inline-block">
                @if($torneo->logo)
                <img src="{{ asset($torneo->logo) }}" alt="Logo Torneo" class="w-24 h-24 object-contain mx-auto rounded-xl bg-white p-2 border border-zinc-200 mb-3" onerror="this.outerHTML='<div class=\'w-24 h-24 mx-auto rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400 text-xs font-bold border border-zinc-200 mb-3\'>N/A</div>'">
                @else
                <div class="w-24 h-24 mx-auto rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400 text-xs font-bold border border-zinc-200 mb-3">Sin logo</div>
                @endif
                <h3 class="text-lg font-bold text-zinc-900 group-hover:text-lime-600 transition-colors">{{ $torneo->nombre }}</h3>
            </a>
        </div>
    </div>

    <!-- Tabla Jugadores -->
    <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-zinc-900">Jugadores inscritos</h2>
            <button type="button" onclick="openModal('inscripcionModal')" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-4 py-2.5 rounded-xl text-sm transition-colors shadow-md flex items-center gap-2 cursor-pointer">
                <i class="bi bi-plus-lg"></i> Inscribir Jugador
            </button>
        </div>

        @if($jugadores->count())
        <div class="overflow-x-auto">
            <table id="tablaJugadores" class="w-full text-left text-sm text-zinc-700">
                <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3 px-4 text-center">#</th>
                        <th class="py-3 px-4 text-center">Foto</th>
                        <th class="py-3 px-4">Nombre</th>
                        <th class="py-3 px-4">Posición</th>
                        <th class="py-3 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @foreach($jugadores as $index => $jugador)
                    <tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="py-3 px-4 text-center font-mono text-zinc-500">{{ $index + 1 }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($jugador->foto)
                            <img src="{{ asset($jugador->foto) }}" alt="Foto Jugador" class="w-10 h-10 object-cover mx-auto rounded-lg bg-white p-0.5 border border-zinc-200" onerror="this.outerHTML='<div class=\'w-10 h-10 mx-auto rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-400 text-[10px] font-bold border border-zinc-200\'>N/A</div>'">
                            @else
                            <div class="w-10 h-10 mx-auto rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-400 text-[10px] font-bold border border-zinc-200">N/A</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-bold text-zinc-900">{{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}</td>
                        <td class="py-3 px-4 text-zinc-600">{{ $jugador->posicion ?? '-' }}</td>
                        <td class="py-3 px-4 text-center space-x-1">
                            <a href="{{ url('/admin/jugadores/'.$jugador->id) }}" class="inline-flex items-center gap-1 bg-cyan-50 hover:bg-cyan-100 text-cyan-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <a href="{{ url('/admin/torneos/'.$torneo->id.'/equipos/'.$equipo->id.'/jugadores/'.$jugador->id.'/eliminar') }}"
                                class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors btn-desinscribir-jugador cursor-pointer"
                                title="Desinscribir del torneo"
                                data-url="{{ url('/admin/torneos/'.$torneo->id.'/equipos/'.$equipo->id.'/jugadores/'.$jugador->id.'/eliminar') }}">
                                <i class="bi bi-x-circle"></i> Quitar
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-zinc-500 text-center py-4">No hay jugadores inscritos todavía.</p>
        @endif
    </div>
</div>

<!-- Modal Inscribir Jugador -->
<div id="inscripcionModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-sm">
    <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl relative">
        <form method="POST" action="{{ url('/admin/torneos/'.$torneo->id.'/equipos/'.$equipo->id.'/jugadores/inscribir') }}">
            @csrf
            <div class="flex items-center justify-between p-6 border-b border-zinc-200">
                <h5 class="text-lg font-bold text-zinc-900">Inscribir Jugador en el Torneo</h5>
                <button type="button" onclick="closeModal('inscripcionModal')" class="text-zinc-500 hover:text-zinc-900 p-1.5 rounded-lg hover:bg-zinc-100 cursor-pointer">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label for="jugador_id" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Seleccionar Jugador</label>
                    <select name="jugador_id" id="jugador_id" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" required>
                        <option value="">Selecciona un jugador...</option>
                        @foreach($jugadoresDisponibles ?? [] as $jugador)
                        <option value="{{ $jugador->id }}">{{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-6 border-t border-zinc-200 bg-zinc-50">
                <button type="button" onclick="closeModal('inscripcionModal')" class="bg-zinc-200 hover:bg-zinc-300 text-zinc-800 font-medium px-4 py-2.5 rounded-xl text-sm transition-colors cursor-pointer">Cancelar</button>
                <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md cursor-pointer">Inscribir</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    document.addEventListener('DOMContentLoaded', function() {
        const createAndSubmitDeleteForm = (url) => {
            const form = document.createElement('form');
            form.method = 'POST'; form.action = url;
            const csrf = document.createElement('input'); csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
            const method = document.createElement('input'); method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
            form.appendChild(csrf); form.appendChild(method); document.body.appendChild(form); form.submit();
        };

        document.querySelectorAll('.btn-desinscribir-jugador').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¿Deseas desinscribir a este jugador del torneo?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#a3e635',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Sí, quitar',
                    cancelButtonText: 'Cancelar',
                    background: '#ffffff',
                    color: '#18181b'
                }).then((res) => {
                    if (res.isConfirmed) {
                        createAndSubmitDeleteForm(this.dataset.url);
                    }
                });
            });
        });
    });
</script>
@endpush
