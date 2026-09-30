@extends('user.layouts.app')

@section('title', 'Suscripción Confirmada - MiFantasy')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="card shadow-sm border-0 rounded-4 p-5">
                <div class="mb-4">
                    <div class="rounded-circle bg-success-subtle text-success d-inline-flex p-3 align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                            <path d="M12.736 3.97a.75.75 0 0 1 1.02 1.06l-7.25 7.5a.75.75 0 0 1-1.08.02L2.324 9.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 6.708-6.708a.75.75 0 0 1 .55-.247z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="fw-bold mb-2">¡Suscripción Procesada!</h3>
                <p class="text-muted mb-4">
                    Tu pago ha sido registrado correctamente a través de Stripe. Tu espacio de gestión y roles administrativos se están aprovisionando automáticamente en segundo plano.
                </p>
                <div class="d-grid gap-2 col-sm-8 mx-auto">
                    <a href="{{ route('home') }}" class="btn btn-primary">Ir al Panel Principal</a>
                    <a href="{{ route('subscription.portal') }}" class="btn btn-outline-secondary btn-sm">Gestionar Facturación</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
