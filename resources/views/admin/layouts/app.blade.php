@extends('layouts.base')

@section('head')
<title>@yield('title', 'Panel de Administración')</title>

{{-- Vite / Tailwind --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- Estilos locales --}}
<link rel="stylesheet" href="{{ asset('assets/css/fantasy.css') }}">

{{-- DataTables CSS --}}
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    html,
    body {
        height: auto !important;
        min-height: 100vh !important;
    }
</style>

@stack('styles')
@endsection

@section('body_class', 'bg-zinc-50 text-zinc-950 min-h-screen flex flex-col font-sans antialiased selection:bg-lime-400 selection:text-zinc-950')
@section('body')
{{-- Header --}}
@include('admin.layouts.header')

{{-- Contenido principal --}}
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-1">
    @yield('content')
</main>
@include('admin.layouts.menu-movil')
{{-- Footer --}}
@include('admin.layouts.footer')

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

{{-- DataTables Bundle --}}
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- SortableJS --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endsection