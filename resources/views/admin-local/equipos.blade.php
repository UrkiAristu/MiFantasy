@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">GestiÃ³n de Equipos</h1>
            <p class="text-gray-500">Listado de equipos inscritos y control de plantillas.</p>
        </div>
        <button type="button" class="bg-emerald-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-emerald-700 transition flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Inscribir Equipo
        </button>
    </div>

    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <input type="text" placeholder="Buscar equipo..." class="bg-white border border-gray-300 rounded-lg text-sm px-3 py-2 w-72 focus:ring-emerald-500 focus:border-emerald-500">
            <span class="text-xs text-gray-500 font-medium">Mostrando todos los equipos</span>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50/50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="p-4">Equipo</th>
                    <th class="p-4">Jugadores</th>
                    <th class="p-4">Estado</th>
                    <th class="p-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 font-semibold text-gray-900 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">R</div>
                        Real Madrid
                    </td>
                    <td class="p-4 text-gray-600">7 jugadores</td>
                    <td class="p-4"><span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Inscrito</span></td>
                    <td class="p-4 text-right">
                        <button class="text-emerald-600 hover:text-emerald-800 font-medium text-xs px-3 py-1.5 bg-emerald-50 rounded-lg transition">Ver Plantilla</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 font-semibold text-gray-900 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold">F</div>
                        FC Barcelona
                    </td>
                    <td class="p-4 text-gray-600">7 jugadores</td>
                    <td class="p-4"><span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Inscrito</span></td>
                    <td class="p-4 text-right">
                        <button class="text-emerald-600 hover:text-emerald-800 font-medium text-xs px-3 py-1.5 bg-emerald-50 rounded-lg transition">Ver Plantilla</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection


