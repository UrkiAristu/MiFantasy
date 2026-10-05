@extends('user.layouts.app')

@section('title', 'Suscripción Confirmada - MiFantasy')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16 text-zinc-200">
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-8 md:p-12 shadow-xl backdrop-blur-sm flex flex-col items-center justify-center min-h-[50vh] space-y-6 text-center max-w-lg mx-auto">
        <div class="w-20 h-20 rounded-full bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
                <path d="M12.736 3.97a.75.75 0 0 1 1.02 1.06l-7.25 7.5a.75.75 0 0 1-1.08.02L2.324 9.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 6.708-6.708a.75.75 0 0 1 .55-.247z"/>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-zinc-100 tracking-tight mb-1">¡Suscripción Procesada!</h3>
        <p class="text-zinc-400 text-sm leading-relaxed max-w-md">
            Tu pago ha sido registrado correctamente a través de Stripe. Tu espacio de gestión y roles administrativos se están aprovisionando automáticamente en segundo plano.
        </p>
        <div class="flex flex-col space-y-3 w-full pt-4">
            <a href="{{ route('home') }}" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold py-3 px-6 rounded-xl transition-colors text-center shadow-md">Ir al Panel Principal</a>
            <a href="{{ route('subscription.portal') }}" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-medium py-2.5 px-6 rounded-xl transition-colors text-center text-sm">Gestionar Facturación</a>
        </div>
    </div>
</div>
@endsection
