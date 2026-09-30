@extends('user.layouts.app')

@section('title', 'Suscripción Cancelada - MiFantasy')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="card shadow-sm border-0 rounded-4 p-5">
                <div class="mb-4">
                    <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex p-3 align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="fw-bold mb-2">Proceso de Pago Cancelado</h3>
                <p class="text-muted mb-4">
                    No se ha realizado ningún cargo en tu tarjeta o cuenta Bizum. Puedes retomar la suscripción en cualquier momento cuando lo desees.
                </p>
                <div class="d-grid gap-2 col-sm-8 mx-auto">
                    <a href="{{ route('subscription.checkout') }}" class="btn btn-primary">Volver a Intentar</a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">Volver al Panel Principal</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
