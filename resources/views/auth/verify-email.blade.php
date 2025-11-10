@extends('auth.layouts.app')

@section('content')
<div class="container text-center mt-5">
    <h1 class="mb-4 text-primary fw-bold">Verifica tu correo electrónico</h1>

    <p class="text-muted">
        Te hemos enviado un enlace de verificación a <strong>{{ Auth::user()->email }}</strong>.
        <br>Por favor, revisa tu bandeja de entrada o spam.
    </p>

    @if (session('message'))
        <div class="alert alert-success mt-4">{{ session('message') }}</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
        @csrf
        <button type="submit" class="btn btn-primary btn-lg">
            Reenviar correo de verificación
        </button>
    </form>
</div>
@endsection
