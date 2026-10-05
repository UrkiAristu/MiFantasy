@extends('user.layouts.app')

@section('title', 'Suscripción Cancelada - MiFantasy')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16 text-zinc-200">
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-8 md:p-12 shadow-xl backdrop-blur-sm flex flex-col items-center justify-center min-h-[50vh] space-y-6 text-center max-w-lg mx-auto">
        <div class="w-20 h-20 rounded-full bg-amber-500/10 text-amber-400 flex items-center justify-center mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-zinc-100 tracking-tight mb-1">Proceso de Pago Cancelado</h3>
        <p class="text-zinc-400 text-sm leading-relaxed max-w-md">
            No se ha realizado ningún cargo en tu tarjeta o cuenta Bizum. Puedes retomar la suscripción en cualquier momento cuando lo desees.
        </p>
        <div class="flex flex-col space-y-3 w-full pt-4">
            <a href="{{ route('subscription.checkout') }}" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold py-3 px-6 rounded-xl transition-colors text-center shadow-md">Volver a Intentar</a>
            <a href="{{ route('home') }}" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-medium py-2.5 px-6 rounded-xl transition-colors text-center text-sm">Volver al Panel Principal</a>
        </div>
    </div>
</div>
@endsection
