@extends('user.layouts.app')

@section('content')
<section class="py-5 bg-light text-center">
    <div class="container">
        <h1 class="display-4 fw-bold text-dark">¡Bienvenido a MiFantasy!</h1>
        <p class="lead text-muted">Crea torneos, ficha jugadores, y compite con tus amigos.</p>
        <img src="{{ asset('assets/media/images/login-fantasy.jpg') }}" class="img-fluid rounded mt-4 shadow"
            alt="Fantasy Banner" style="max-height: 300px;">
    </div>
</section>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm m-2">
                <div class="card-body">
                    <h5 class="card-title">Crear Liguilla</h5>
                    <p class="card-text">Organiza una nueva liguilla y compite contra tus amigos.</p>
                    <a href="#" class="btn btn-primary w-100">Crear</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm m-2">
                <div class="card-body">
                    <h5 class="card-title">Ver Equipos</h5>
                    <p class="card-text">Explora los equipos que compiten en los torneos activos.</p>
                    <a href="#" class="btn btn-primary w-100">Equipos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm m-2">
                <div class="card-body">
                    <h5 class="card-title">Estadísticas</h5>
                    <p class="card-text">Consulta las estadísticas de tus jugadores.</p>
                    <a href="#" class="btn btn-primary w-100">Ver estadísticas</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection