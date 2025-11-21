@extends('auth.layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Recuperar contraseña</h2>

    @if (session('status'))
        <div class="alert alert-success mt-2">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-3">
        @csrf

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
            @error('email')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-primary">Enviar enlace</button>
    </form>
</div>
@endsection
