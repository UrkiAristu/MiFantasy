@extends('user.layouts.app')

@section('title', 'Suscripción SaaS - MiFantasy')

@section('content')
<!-- Rule 5: PCI-SAQ A compliant - No card details stored on-server. Redirection to Stripe Checkout. -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white p-4 text-center rounded-top-4">
                    <h3 class="fw-bold mb-1">Elige tu Plan de Gestión SaaS</h3>
                    <p class="mb-0 text-white-50">Gestiona torneos, equipos, alineaciones y estadísticas con tu propia liga personalizada.</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="card h-100 border text-center p-3">
                                <h5 class="fw-bold text-dark">Básico</h5>
                                <div class="my-2">
                                    <span class="display-6 fw-bold">29€</span><span class="text-muted">/mes</span>
                                </div>
                                <p class="small text-muted">1 liga local independiente</p>
                                <a href="{{ route('subscription.checkout', ['plan' => 'basico']) }}" class="btn btn-outline-primary btn-sm mt-auto">Seleccionar</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-primary border-2 text-center p-3 shadow-sm bg-light">
                                <span class="badge bg-primary mb-2 align-self-center">Recomendado</span>
                                <h5 class="fw-bold text-primary">Pro</h5>
                                <div class="my-2">
                                    <span class="display-6 fw-bold text-primary">79€</span><span class="text-muted">/mes</span>
                                </div>
                                <p class="small text-muted">Múltiples divisiones y torneos</p>
                                <a href="{{ route('subscription.checkout', ['plan' => 'pro']) }}" class="btn btn-primary btn-sm mt-auto">Comenzar</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border text-center p-3">
                                <h5 class="fw-bold text-dark">Enterprise</h5>
                                <div class="my-2">
                                    <span class="display-6 fw-bold">199€</span><span class="text-muted">/mes</span>
                                </div>
                                <p class="small text-muted">Federaciones y grandes complejos</p>
                                <a href="{{ route('subscription.checkout', ['plan' => 'enterprise']) }}" class="btn btn-outline-primary btn-sm mt-auto">Seleccionar</a>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info d-flex align-items-center mb-0" role="alert">
                        <div class="small">
                            <strong>Métodos de pago aceptados:</strong> Tarjetas de Crédito/Débito (Visa, Mastercard, American Express) y <strong>Bizum</strong>. Pago 100% seguro y cifrado procesado mediante Stripe Checkout.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
