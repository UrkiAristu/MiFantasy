@extends('layouts.base')

@section('head')
<title>@yield('title', 'MiFantasy - Autenticación')</title>
@stack('styles')
@endsection

@section('body_class', 'bg-zinc-950 text-zinc-100 min-h-screen flex flex-col selection:bg-lime-400 selection:text-zinc-950')

@section('body')
@include('auth.layouts.header')

<main class="flex-1 flex flex-col">
    @yield('content')
</main>

@include('auth.layouts.footer')
@endsection
