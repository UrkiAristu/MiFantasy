@extends('user.layouts.app')

@section('title', 'Mis Liguillas - MiFantasy')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">

    <!-- Header de Página -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lime-400/10 border border-lime-400/20 text-lime-400 text-xs font-semibold">
                <i class="bi bi-trophy"></i>
                <span>Competición Fantasy</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-100">
                Mis Liguillas
            </h1>
            <p class="text-xs sm:text-sm text-zinc-400">
                Ligas activas en las que estás compitiendo actualmente.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ url('/user/unirseLiguilla') }}"
                class="py-2.5 px-4 bg-zinc-800/80 hover:bg-zinc-800 text-zinc-200 hover:text-zinc-100 font-semibold text-xs rounded-xl border border-zinc-700/80 transition-all flex items-center gap-2"
            >
                <i class="bi bi-bookmark-plus"></i>
                <span>Unirse con código</span>
            </a>
            <a
                href="{{ url('/user/torneos') }}"
                class="py-2.5 px-4 bg-lime-400 hover:bg-lime-300 text-zinc-950 font-semibold text-xs rounded-xl shadow-lg shadow-lime-400/10 hover:shadow-lime-400/20 transition-all flex items-center gap-2"
            >
                <i class="bi bi-plus-lg font-bold"></i>
                <span>Crear Liguilla</span>
            </a>
        </div>
    </div>

    @if($liguillasUsuario->isEmpty())
        <!-- Estado Vacío Bento -->
        <div class="bg-zinc-900/40 border border-zinc-800/80 backdrop-blur-xl rounded-3xl p-8 sm:p-16 text-center space-y-6 max-w-2xl mx-auto shadow-2xl shadow-zinc-950/40">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-zinc-800/60 border border-zinc-700/60 text-zinc-400 flex items-center justify-center text-3xl">
                <i class="bi bi-trophy"></i>
            </div>
            <div class="space-y-2">
                <h2 class="text-xl font-bold tracking-tight text-zinc-100">Aún no participas en ninguna liguilla</h2>
                <p class="text-xs sm:text-sm text-zinc-400 max-w-md mx-auto">
                    Únete a una liga existente con un código de invitación o crea tu propia liguilla seleccionando un torneo oficial.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                <a
                    href="{{ url('/user/unirseLiguilla') }}"
                    class="w-full sm:w-auto py-2.5 px-5 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-semibold text-xs rounded-xl border border-zinc-700 transition-all flex items-center justify-center gap-2"
                >
                    <i class="bi bi-bookmark-plus"></i>
                    <span>Unirse con código</span>
                </a>
                <a
                    href="{{ url('/user/torneos') }}"
                    class="w-full sm:w-auto py-2.5 px-5 bg-lime-400 hover:bg-lime-300 text-zinc-950 font-semibold text-xs rounded-xl shadow-lg shadow-lime-400/10 hover:shadow-lime-400/20 transition-all flex items-center justify-center gap-2"
                >
                    <i class="bi bi-plus-lg font-bold"></i>
                    <span>Crear nueva liga</span>
                </a>
            </div>
        </div>
    @else
        <!-- Carousel Swiper Coverflow -->
        <div class="relative py-4">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper py-6">
                    @foreach($liguillasUsuario as $liguilla)
                        <div class="swiper-slide flex justify-center">
                            <div
                                onclick="window.location='{{ url('/user/liguillas/'.$liguilla->id) }}'"
                                class="group w-full max-w-[340px] bg-zinc-900/90 border border-zinc-800 hover:border-lime-500/40 backdrop-blur-xl rounded-3xl p-6 sm:p-7 shadow-2xl shadow-zinc-950/80 cursor-pointer transition-all duration-300 hover:-translate-y-1 text-center space-y-6 flex flex-col justify-between"
                            >
                                <!-- Logo / Icono Torneo -->
                                <div class="space-y-3">
                                    <div class="relative w-28 h-28 mx-auto rounded-2xl bg-zinc-950/80 border border-zinc-800/80 p-3 flex items-center justify-center group-hover:border-lime-500/30 transition-all">
                                        @if(!empty($liguilla->torneo->logo))
                                            <img
                                                src="{{ asset($liguilla->torneo->logo) }}"
                                                alt="{{ $liguilla->torneo->nombre }}"
                                                class="max-h-full max-w-full object-contain filter drop-shadow-md"
                                                loading="lazy"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                            >
                                            <div class="hidden text-amber-400 text-4xl items-center justify-center">
                                                <i class="bi bi-trophy-fill"></i>
                                            </div>
                                        @else
                                            <div class="text-amber-400 text-4xl flex items-center justify-center">
                                                <i class="bi bi-trophy-fill"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Títulos -->
                                    <div class="space-y-1">
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-zinc-800/80 border border-zinc-700/60 text-zinc-400 text-[11px] font-medium">
                                            <i class="bi bi-shield-shaded text-zinc-500"></i>
                                            <span>{{ $liguilla->torneo->nombre }}</span>
                                        </div>
                                        <h2 class="text-xl font-bold tracking-tight text-zinc-100 group-hover:text-lime-400 transition-colors line-clamp-1">
                                            {{ $liguilla->nombre }}
                                        </h2>
                                    </div>
                                </div>

                                <!-- Métricas Bento -->
                                <div class="grid grid-cols-2 gap-3 py-3 border-y border-zinc-800/80">
                                    <div class="bg-zinc-950/60 border border-zinc-800/60 rounded-xl p-2.5 text-center">
                                        <span class="block text-[10px] uppercase font-semibold tracking-wider text-zinc-500">Posición</span>
                                        <span class="text-lg font-bold font-mono text-zinc-100">
                                            #{{ $liguilla->posicion_usuario ?? $liguilla->pivot->puesto ?? 'N/D' }}
                                        </span>
                                    </div>
                                    <div class="bg-zinc-950/60 border border-zinc-800/60 rounded-xl p-2.5 text-center">
                                        <span class="block text-[10px] uppercase font-semibold tracking-wider text-zinc-500">Puntos</span>
                                        <span class="text-lg font-bold font-mono text-lime-400">
                                            {{ $liguilla->puntos_usuario ?? $liguilla->pivot->puntos ?? 0 }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Acciones Card -->
                                <div class="space-y-2 pt-1">
                                    <button
                                        type="button"
                                        onclick="event.stopPropagation(); compartirEnlace('{{ $liguilla->codigo_unico }}')"
                                        class="w-full py-2.5 px-3 bg-zinc-800/80 hover:bg-zinc-800 text-zinc-300 hover:text-zinc-100 font-semibold text-xs rounded-xl border border-zinc-700/80 hover:border-zinc-600 transition-all flex items-center justify-center gap-2 cursor-pointer"
                                    >
                                        <i class="bi bi-share text-xs"></i>
                                        <span>Compartir código</span>
                                    </button>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Controles Swiper -->
                <div class="swiper-button-next !text-lime-400 after:!text-xl !w-10 !h-10 !bg-zinc-900/80 !border !border-zinc-800 !backdrop-blur-md !rounded-full shadow-lg"></div>
                <div class="swiper-button-prev !text-lime-400 after:!text-xl !w-10 !h-10 !bg-zinc-900/80 !border !border-zinc-800 !backdrop-blur-md !rounded-full shadow-lg"></div>
                <div class="swiper-pagination !bottom-0"></div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
<style>
    .mySwiper {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        padding-bottom: 40px !important;
    }

    .swiper-pagination-bullet {
        background: #71717a !important;
        opacity: 0.5;
        transition: all 0.2s ease;
    }

    .swiper-pagination-bullet-active {
        background: #a3e635 !important;
        opacity: 1;
        width: 20px;
        border-radius: 9999px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script>
    function compartirEnlace(codigo) {
        const enlace = "{{ url('/user/unirseLiguilla') }}?codigo=" + codigo;

        if (navigator.share) {
            navigator.share({
                title: 'Únete a mi liguilla en MiFantasy',
                text: 'Usa mi código para unirte a la competición:',
                url: enlace
            }).catch(err => console.error('Error al compartir:', err));
        } else {
            navigator.clipboard.writeText(enlace).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: '¡Enlace copiado!',
                    text: 'El enlace de invitación se ha copiado al portapapeles.',
                    background: '#18181b',
                    color: '#f4f4f5',
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'border border-zinc-800 rounded-2xl' }
                });
            }).catch(err => {
                Swal.fire({
                    icon: 'info',
                    title: 'Código de liga',
                    text: codigo,
                    background: '#18181b',
                    color: '#f4f4f5',
                    customClass: { popup: 'border border-zinc-800 rounded-2xl' }
                });
            });
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        if (document.querySelector('.mySwiper')) {
            new Swiper(".mySwiper", {
                effect: "coverflow",
                grabCursor: true,
                autoplay: false,
                slidesPerView: 1,
                centeredSlides: true,
                spaceBetween: 30,
                initialSlide: 0,
                loop: {{ ($liguillasUsuario?->count() ?? 0) > 1 ? 'true' : 'false' }},
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                    }
                },
                coverflowEffect: {
                    rotate: 15,
                    stretch: 0,
                    depth: 120,
                    modifier: 1,
                    slideShadows: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
            });
        }
    });
</script>
@endpush
