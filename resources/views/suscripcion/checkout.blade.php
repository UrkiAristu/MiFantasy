@extends('user.layouts.app')

@section('title', 'Suscripción SaaS - MiFantasy')

@section('content')
<!-- Rule 5: PCI-SAQ A compliant - No card details stored on-server. Redirection to Stripe Checkout. -->
<div class="max-w-4xl mx-auto px-4 py-12 text-zinc-200">
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm">
        <div class="bg-zinc-900 border-b border-zinc-800 p-8 text-center">
            <h3 class="text-2xl font-bold tracking-tight text-zinc-100 mb-2">Elige tu Plan de Gestión SaaS</h3>
            <p class="text-zinc-400 text-sm max-w-xl mx-auto">Gestiona torneos, equipos, alineaciones y estadísticas con tu propia liga personalizada.</p>
        </div>
        <div class="p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Plan Básico -->
                <div class="bg-zinc-900/90 border border-zinc-800 rounded-2xl p-6 flex flex-col text-center transition-all hover:border-zinc-700">
                    <h5 class="text-lg font-bold text-zinc-200 mb-3">Básico</h5>
                    <div class="my-3">
                        <span class="text-4xl font-extrabold text-zinc-100">29€</span><span class="text-zinc-400 text-sm">/mes</span>
                    </div>
                    <p class="text-xs text-zinc-400 mb-6">1 liga local independiente</p>
                    <a href="{{ route('subscription.checkout', ['plan' => 'basico']) }}" class="mt-auto border-2 border-teal-400 text-teal-400 hover:bg-teal-400/10 font-medium py-2 px-4 rounded-xl text-sm transition-colors block text-center">Seleccionar</a>
                </div>

                <!-- Plan Pro (Recomendado) -->
                <div class="bg-zinc-900 border-2 border-lime-400/60 rounded-2xl p-6 flex flex-col text-center relative shadow-lg shadow-lime-400/5">
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-lime-400 text-zinc-950 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Recomendado</span>
                    <h5 class="text-lg font-bold text-lime-400 mb-3 mt-1">Pro</h5>
                    <div class="my-3">
                        <span class="text-4xl font-extrabold text-lime-400">79€</span><span class="text-zinc-400 text-sm">/mes</span>
                    </div>
                    <p class="text-xs text-zinc-400 mb-6">Múltiples divisiones y torneos</p>
                    <a href="{{ route('subscription.checkout', ['plan' => 'pro']) }}" class="mt-auto bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold py-2 px-4 rounded-xl text-sm transition-colors block text-center shadow-md">Comenzar</a>
                </div>

                <!-- Plan Enterprise -->
                <div class="bg-zinc-900/90 border border-zinc-800 rounded-2xl p-6 flex flex-col text-center transition-all hover:border-zinc-700">
                    <h5 class="text-lg font-bold text-zinc-200 mb-3">Enterprise</h5>
                    <div class="my-3">
                        <span class="text-4xl font-extrabold text-zinc-100">199€</span><span class="text-zinc-400 text-sm">/mes</span>
                    </div>
                    <p class="text-xs text-zinc-400 mb-6">Federaciones y grandes complejos</p>
                    <a href="{{ route('subscription.checkout', ['plan' => 'enterprise']) }}" class="mt-auto border-2 border-teal-400 text-teal-400 hover:bg-teal-400/10 font-medium py-2 px-4 rounded-xl text-sm transition-colors block text-center">Seleccionar</a>
                </div>
            </div>

            <div class="bg-zinc-950/60 border border-zinc-800/80 rounded-xl p-4 flex items-center space-x-3 text-zinc-300 text-xs">
                <svg class="w-5 h-5 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <strong class="text-zinc-200">Métodos de pago aceptados:</strong> Tarjetas de Crédito/Débito (Visa, Mastercard, American Express) y <strong class="text-zinc-200">Bizum</strong>. Pago 100% seguro y cifrado procesado mediante Stripe Checkout.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
