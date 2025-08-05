<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administración')</title>

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('assets/media/logos/logo-fantasy.png') }}" type="image/png">

    {{-- Bootstrap 5 --}}
    {{-- Estilos globales --}}
    <link rel="stylesheet" href="{{ asset('assets/plugins/global/plugins.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fantasy.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.bundle.css') }}">

    {{-- DataTables + Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />



    @stack('styles')
</head>

<body class="bg-light">

    {{-- Header --}}
    @include('admin.layouts.header')

    {{-- Contenido principal --}}
    <main class="container py-4">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('admin.layouts.footer')

    {{-- Scripts globales --}}
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>

    {{-- jQuery y DataTables --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    {{-- Inicialización de DataTables --}}

    @stack('scripts')
</body>

</html>