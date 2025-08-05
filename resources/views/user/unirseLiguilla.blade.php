@extends('user.layouts.app')

@section('title', 'Unirse a Liguilla')

@section('content')
<div class="container my-5 d-flex flex-column align-items-center">
    <h1 class="mb-4">Unirse a una Liguilla</h1>

    @if(session('success'))
    <div class="alert alert-success w-100 text-center">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger w-100 text-center">
        {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
        {{ $error }}<br>
        @endforeach
    </div>
    @endif

    <form action="{{ url('/user/liguillas/unirse') }}" method="POST" class="w-100" style="max-width: 600px;">
        @csrf
        <div class="mb-3">
            <label for="codigo" class="form-label fs-4 fw-bold">Código de la Liguilla</label>
            <input
                type="text"
                name="codigo"
                id="codigo"
                class="form-control form-control-lg text-center fs-2"
                placeholder="Introduce el código aquí"
                required
                autofocus
                style="height: 70px; letter-spacing: 5px; text-transform: uppercase;">
        </div>
        <button type="submit" class="btn btn-primary btn-lg w-100">Unirme</button>
    </form>
</div>
@endsection