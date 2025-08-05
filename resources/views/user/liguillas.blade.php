@extends('user.layouts.app')

@section('title', 'Mis Liguillas')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Liguillas en las que compites</h1>

    @if($liguillasUsuario->isEmpty())
    <p>No estás participando en ninguna liguilla.</p>
    @else
    <div class="swiper mySwiper py-5">
        <div class="swiper-wrapper">
            @foreach($liguillasUsuario as $liguilla)
            <div class="swiper-slide d-flex justify-content-center">
                <div class="card text-center shadow p-5" style="width: 320px;">
                    @if($liguilla->torneo->logo && file_exists(public_path($liguilla->torneo->logo)))
                    <img src="{{ asset($liguilla->torneo->logo) }}" alt="{{ $liguilla->torneo->nombre }}" style="height: 180px; object-fit: contain; padding: 20px;">
                    @else
                    <!-- Aquí pones la copa grande como fallback -->
                    <i class="fa fa-trophy" style="font-size: 6rem; color: gold; padding: 20px;"></i>
                    {{-- O una imagen local: --}}
                    {{-- <img src="{{ asset('images/copa-grande.png') }}" alt="Copa" style="height: 180px; object-fit: contain; padding: 20px;"> --}}
                    @endif <div class="card-body">
                        <h5 class="card-title">{{ $liguilla->nombre }}</h5>
                        <p class="card-text mb-1"><strong>Torneo:</strong> {{ $liguilla->torneo->nombre }}</p>
                        <p class="card-text mb-1"><strong>Posición:</strong> {{ $liguilla->pivot->posicion ?? 'N/D' }}</p>
                        <p class="card-text"><strong>Puntos:</strong> {{ $liguilla->pivot->puntos ?? 0 }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Flechas -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>

        <!-- Paginación -->
        <div class="swiper-pagination"></div>
    </div>
    @endif
</div>
@endsection
@push('styles')
<style>
    .mySwiper {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
        padding: 20px 0;
    }

    @media (max-width: 768px) {
        .mySwiper {
            padding: 0 10px;
        }
    }
</style>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper(".mySwiper", {
        effect: "coverflow",
        grabCursor: true,
        loop: true,
        autoplay: false,
        slidesPerView: 1,
        centeredSlides: true,
        spaceBetween: 20,
        initialSlide: 0,
        loop: true,
        coverflowEffect: {
            rotate: 50,
            stretch: 0,
            depth: 150,
            modifier: 1,
            slideShadows: true,
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
</script>
@endpush