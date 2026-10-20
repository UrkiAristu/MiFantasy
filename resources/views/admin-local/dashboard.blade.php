@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Panel de Competición Local</h1>
            <p class="text-gray-500">Bienvenido, {{ Auth::user()->name }}</p>
        </div>

        <!-- Selector de Torneo Moderno -->
        <div>
            <form action="{{ route('admin.local.dashboard') }}" method="GET" class="flex items-center gap-2">
                <select name="torneo_id" onchange="this.form.submit()" class="bg-white border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm min-w-[240px]">
                    <option value="">🌐 Todos los Torneos</option>
                    @foreach($torneos as $t)
                        <option value="{{ $t->id }}" {{ $torneoId == $t->id ? 'selected' : '' }}>🏆 {{ $t->nombre }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if($torneoId)
        @php $torneoActual = $torneos->firstWhere('id', $torneoId); @endphp
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-emerald-600 text-white p-2 rounded-lg">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Filtrando por Torneo Activo</span>
                    <h2 class="text-base font-bold text-emerald-900">{{ $torneoActual?->nombre ?? 'Torneo Seleccionado' }}</h2>
                </div>
            </div>
            <a href="{{ route('admin.local.dashboard') }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-900 bg-white border border-emerald-300 px-3 py-1.5 rounded-lg shadow-sm transition">
                Ver todos
            </a>
        </div>
    @endif

    <!-- Grid de 4 KPIs (Dinámico según contexto) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
        @if(!$torneoId)
            <!-- Torneos Activos (Solo en vista global) -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5 flex items-center gap-4">
                <div class="bg-emerald-100 text-emerald-700 p-3 rounded-lg text-xl flex items-center justify-center">
                    <i class="bi bi-trophy"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Torneos Activos</p>
                    <h4 class="text-2xl font-bold text-gray-900">{{ $torneosActivos }}</h4>
                </div>
            </div>
        @else
            <!-- Estado del Torneo Actual (Reemplaza al KPI redundante) -->
            @php $torneoActual = $torneos->firstWhere('id', $torneoId); @endphp
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5 flex items-center gap-4">
                <div class="bg-blue-100 text-blue-700 p-3 rounded-lg text-xl flex items-center justify-center">
                    <i class="bi bi-info-circle"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Estado del Torneo</p>
                    <h4 class="text-xl font-bold text-gray-900 capitalize">{{ $torneoActual->estado ?? 'Activo' }}</h4>
                </div>
            </div>
        @endif

        <!-- Equipos Inscritos -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5 flex items-center gap-4">
            <div class="bg-emerald-100 text-emerald-700 p-3 rounded-lg text-xl flex items-center justify-center">
                <i class="bi bi-shield"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Equipos Inscritos</p>
                <h4 class="text-2xl font-bold text-gray-900">{{ $equiposInscritos }}</h4>
            </div>
        </div>

        <!-- Jugadores en Base -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5 flex items-center gap-4">
            <div class="bg-emerald-100 text-emerald-700 p-3 rounded-lg text-xl flex items-center justify-center">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Jugadores en Base</p>
                <h4 class="text-2xl font-bold text-gray-900">{{ $jugadoresBase }}</h4>
            </div>
        </div>

        <!-- Partidos Pendientes -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5 flex items-center gap-4">
            <div class="bg-emerald-100 text-emerald-700 p-3 rounded-lg text-xl flex items-center justify-center">
                <i class="bi bi-calendar"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Partidos Pendientes</p>
                <h4 class="text-2xl font-bold text-gray-900">{{ $partidosPendientes }}</h4>
            </div>
        </div>
    </div>

    <!-- Grid de Contenido (2/3 izquierda, 1/3 derecha) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <!-- Izquierda (Colspan 2 - Últimos Partidos) -->
        <div class="lg:col-span-2 bg-white border border-gray-200 shadow-sm rounded-xl p-6 flex flex-col justify-between">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Últimos Partidos</h3>
            @forelse ($ultimosPartidos as $partido)
                <div class="border-b border-gray-100 py-3 flex justify-between items-center">
                    <div>
                        <p class="font-medium text-gray-900">
                            {{ $partido->equipoLocal->nombre }} vs {{ $partido->equipoVisitante->nombre }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $partido->jornada->torneo->nombre }} | {{ $partido->jornada->nombre }} - {{ $partido->fecha_partido->format('d/m H:i') }}</p>
                    </div>
                    <div class="text-right">
                        @if ($partido->estado === 'jugado')
                            <span class="font-bold text-gray-900">{{ $partido->goles_local }} - {{ $partido->goles_visitante }}</span>
                        @else
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-amber-100 text-amber-800">Programado</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="border-dashed border-2 border-gray-300 bg-gray-50 rounded-xl py-16 flex flex-col items-center justify-center text-center">
                    <i class="bi bi-inbox text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-500 font-medium">No hay partidos recientes en tu torneo</p>
                    <a href="{{ route('admin.local.jornadas.create') }}" class="bg-emerald-600 text-white font-semibold px-5 py-2.5 rounded-lg mt-4 hover:bg-emerald-700 transition">
                        Crear / Registrar Partido
                    </a>
                </div>
            @endforelse
            @if($ultimosPartidos->isNotEmpty())
                <div class="mt-4">
                    <button type="button" class="w-full text-sm text-emerald-600 font-semibold hover:text-emerald-800 transition">
                        Ver todos los partidos
                    </button>
                </div>
            @endif
        </div>

        <!-- Derecha (Acciones Rápidas) -->
        <div class="bg-gray-50 border border-gray-200 shadow-sm rounded-xl p-5">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Gestión Rápida</h3>
            <div class="grid grid-cols-1 gap-3">
                <a href="{{ route('admin.local.equipos') }}" class="flex items-center gap-4 p-4 bg-white border border-gray-200 rounded-xl hover:border-emerald-500 hover:shadow-md transition-all group">
                    <div class="bg-emerald-100 text-emerald-600 p-3 rounded-lg group-hover:bg-emerald-600 group-hover:text-white transition">
                        <i class="bi bi-shield text-xl"></i>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900">Ver Equipos</div>
                        <div class="text-xs text-gray-500">Inscripciones y plantillas</div>
                    </div>
                </a>
                <a href="{{ route('admin.local.jornadas.create') }}" class="flex items-center gap-4 p-4 bg-white border border-gray-200 rounded-xl hover:border-emerald-500 hover:shadow-md transition-all group">
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition">
                        <i class="bi bi-calendar-plus text-xl"></i>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900">Crear Jornada</div>
                        <div class="text-xs text-gray-500">Programar nueva fecha</div>
                    </div>
                </a>
                <button type="button" onclick="confirmarCierreClasificacion()" class="flex items-center gap-4 p-4 w-full bg-white border border-gray-200 rounded-xl hover:border-red-500 hover:shadow-md transition-all group text-left">
                    <div class="bg-red-100 text-red-600 p-3 rounded-lg group-hover:bg-red-600 group-hover:text-white transition">
                        <i class="bi bi-lock text-xl"></i>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900">Cerrar Clasificación</div>
                        <div class="text-xs text-gray-500">Congelar puntos actuales</div>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmarCierreClasificacion() {
        Swal.fire({
            title: '¿Cerrar Clasificación?',
            text: "Esta acción congelará los puntos de la jornada actual y calculará los puestos definitivos.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cerrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                    '¡Clasificación Cerrada!',
                    'Los puntos han sido procesados correctamente.',
                    'success'
                );
            }
        })
    }
</script>
@endsection
