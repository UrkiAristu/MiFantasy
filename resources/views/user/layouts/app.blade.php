@extends('layouts.base')

@section('head')
<title>@yield('title', 'MiFantasy - Inicio')</title>

<!-- Estilos globales -->
<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/fantasy.css') }}" rel="stylesheet" />

<style>
    .mobile-menu {
        position: fixed;
        top: 0;
        left: -100%;
        width: 250px;
        height: 100%;
        background-color: rgb(0, 0, 0);
        color: white;
        padding: 2rem 1rem;
        transition: left 0.3s ease-in-out;
        z-index: 1050;
    }

    .mobile-menu.show {
        left: 0;
    }

    .mobile-menu a {
        color: white;
        display: block;
        margin: 1rem 0;
        font-weight: 600;
    }

    .hamburger {
        border: none;
        background: none;
        color: white;
        font-size: 1.5rem;
    }

    main {
        padding-bottom: 50px !important;
    }

    body {
        padding-bottom: 0px !important;
    }

    @media (min-width: 992px) {
        .hamburger {
            display: none;
        }
    }
</style>
@stack('styles')
@endsection

@section('body_class', 'bg-body')
@section('body')
@include('user.layouts.header')

<main class="bg-light flex-fill">
    @yield('content')
</main>

@include('user.layouts.menu-movil')
@include('user.layouts.footer')
@endsection