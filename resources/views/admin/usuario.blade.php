@extends('admin.layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Detalle del Usuario</h1>

    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>¡Errores encontrados!</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ url('/admin/usuarios/'.$usuario->id.'/editar') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Datos del Usuario</h5>

                <div class="mb-3">
                    <label for="nombreUsuario" class="form-label">Nombre de Usuario</label>
                    <input type="text" class="form-control" id="nombreUsuario" name="nombreUsuario" value="{{ old('nombreUsuario', $usuario->nombreUsuario) }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
                </div>

                <div class="mb-3">
                    <label for="admin" class="form-label">¿Es Administrador?</label>
                    <select name="admin" id="admin" class="form-select">
                        <option value="1" {{ old('admin', $usuario->admin) ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ !old('admin', $usuario->admin) ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="activo" class="form-label">¿Está Activo?</label>
                    <select name="activo" id="activo" class="form-select">
                        <option value="1" {{ old('activo', $usuario->activo) ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ !old('activo', $usuario->activo) ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    <a href="{{ url('/admin/usuarios') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection