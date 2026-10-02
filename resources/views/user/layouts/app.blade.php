@extends('layouts.base')

@section('head')
<title>@yield('title', 'MiFantasy - Panel de Usuario')</title>
@stack('styles')
@endsection

@section('body_class', 'bg-zinc-950 text-zinc-100 min-h-screen flex flex-col font-sans antialiased selection:bg-lime-400 selection:text-zinc-950')

@section('body')
@include('user.layouts.header')

<main class="flex-1 pb-24 lg:pb-12">
    @yield('content')
</main>

@include('user.layouts.menu-movil')
@include('user.layouts.footer')

{{-- Interceptor Global de Notificaciones Toast SweetAlert2 --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#18181b',
                color: '#f4f4f5',
                customClass: {
                    popup: 'border border-zinc-800 rounded-xl shadow-2xl'
                }
            });

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: {!! json_encode(session('success')) !!}
                });
            @endif

            @if(session('status'))
                Toast.fire({
                    icon: 'info',
                    title: {!! json_encode(session('status')) !!}
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: {!! json_encode(session('error')) !!}
                });
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    Toast.fire({
                        icon: 'error',
                        title: {!! json_encode($error) !!}
                    });
                @endforeach
            @endif
        }
    });
</script>
@endsection
