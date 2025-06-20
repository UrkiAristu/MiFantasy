@extends('user.layouts.app')

@section('content')
<section class="py-1 bg-light text-center" style=" min-height: 300px;
    background-image: url('{{ asset('assets/media/images/login-fantasy.jpg') }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;">
    <div class="m-5">
        <div class="container contenedor-blanco">
            <h1 class="display-4 fw-bold text-dark">¡Bienvenido a MiFantasy!</h1>
            <p class="lead text-muted">Crea liguillas, ficha jugadores, y compite con tus amigos.</p>
            <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}"
                class="img-fluid rounded m-4 shadow logo-fantasy"
                alt="Fantasy Logo">
        </div>
    </div>
</section>

<section class="container py-5 mt-1">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm m-2">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Crear Liguilla</h5>
                    <p class="card-text">Organiza una nueva liguilla y compite contra tus amigos.</p>
                    <a href="#" class="btn btn-primary w-100 mt-auto">Crear</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm m-2">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Ver Equipos</h5>
                    <p class="card-text">Explora los equipos que compiten en los torneos activos.</p>
                    <a href="#" class="btn btn-primary w-100 mt-auto">Equipos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm m-2">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Estadísticas</h5>
                    <p class="card-text">Consulta las estadísticas de tus jugadores.</p>
                    <a href="#" class="btn btn-primary w-100 mt-auto">Ver estadísticas</a>
                </div>
            </div>
        </div>

    </div>
</section>
<style>
    .fondo-fantasy {
        min-height: 400px;
        background-image: url('{{ asset(' assets/media/images/login-fantasy.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .contenedor-blanco {
        background-color: rgba(255, 255, 255, 0.8);
        padding: 2rem;
        border-radius: 0.5rem;
        min-height: 350px;
    }

    .logo-fantasy {
        max-height: 250px;
    }

    @media (max-width: 768px) {
        .fondo-fantasy {
            min-height: 200px;
        }

        .logo-fantasy {
            max-height: 150px;
        }
    }
</style>
@endsection