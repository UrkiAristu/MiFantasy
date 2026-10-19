@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-4">
    <h1 class="text-2xl font-bold text-white">Panel de Competición Local</h1>
    <p class="text-zinc-300">Bienvenido, {{ Auth::user()->name }}</p>
</div>
@endsection
