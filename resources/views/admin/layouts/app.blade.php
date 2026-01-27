@extends('layouts.base')

@section('head')
<title>@yield('title', 'Panel de Administración')</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#181c32">

{{-- Bootstrap 5 --}}
{{-- Estilos globales --}}
<link rel="stylesheet" href="{{ asset('assets/plugins/global/plugins.bundle.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/fantasy.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.bundle.css') }}">

{{-- DataTables + Bootstrap 5 --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link href="/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

@stack('styles')
@endsection

@section('body_class', 'bg-light mb-6')
@section('body')
{{-- Header --}}
@include('admin.layouts.header')

{{-- Contenido principal --}}
<main class="bg-light py-4 px-3 px-md-4 container-lg mb-6 flex-fill" style="padding-bottom: 50px !important;">
    @yield('content')
</main>
@include('admin.layouts.menu-movil')
{{-- Footer --}}
@include('admin.layouts.footer')

{{-- Scripts globales --}}
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

{{-- jQuery y DataTables --}}
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- SortableJS --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggler = document.querySelector('.navbar-toggler');
        const navbar = document.getElementById('adminNavbar');

        if (toggler && navbar) {
            toggler.addEventListener('click', function() {
                const bsCollapse = bootstrap.Collapse.getInstance(navbar) ||
                    new bootstrap.Collapse(navbar, {
                        toggle: false
                    });

                const isOpen = navbar.classList.contains('show');

                if (isOpen) {
                    // Lo estás cerrando
                    bsCollapse.hide();
                } else {
                    // Lo estás abriendo
                    bsCollapse.show();

                    // 👇 Si estás en móvil, sube arriba para que el menú se vea
                    if (window.innerWidth < 992) {
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        }

        // Cerrar automáticamente cuando se pulsa un enlace en móvil
        document.querySelectorAll('#adminNavbar .nav-link').forEach(function(el) {
            el.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navbar);
                    if (bsCollapse) {
                        bsCollapse.hide();
                    }
                }
            });
        });
    });
</script>

@stack('scripts')
@endsection