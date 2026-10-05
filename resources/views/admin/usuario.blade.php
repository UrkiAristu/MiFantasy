@extends('admin.layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 text-zinc-900">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-zinc-900">Detalle del Usuario</h1>
        <a href="{{ url('/admin/usuarios') }}" class="bg-zinc-200 hover:bg-zinc-300 text-zinc-800 font-medium px-4 py-2 rounded-xl text-sm transition-colors">Volver</a>
    </div>

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

    <form action="{{ url('/admin/usuarios/'.$usuario->id.'/editar') }}" method="POST" class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6">
        @csrf
        <h2 class="text-lg font-bold text-zinc-900 mb-6">Datos del Usuario</h2>

        <div class="space-y-4">
            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Nombre de Usuario</label>
                <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="name" name="name" value="{{ old('name', $usuario->name) }}" required>
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Correo Electrónico</label>
                <input type="email" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="admin" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">¿Es Administrador?</label>
                    <select name="admin" id="admin" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500">
                        <option value="1" {{ old('admin', $usuario->admin) ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ !old('admin', $usuario->admin) ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div>
                    <label for="active" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">¿Está Activo?</label>
                    <select name="active" id="active" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500">
                        <option value="1" {{ old('active', $usuario->active) ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ !old('active', $usuario->active) ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 border-t border-zinc-200 pt-4">
                <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Guardar Cambios</button>
            </div>
        </div>
    </form>
</div>
@endsection
