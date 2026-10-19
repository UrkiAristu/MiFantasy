@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-4">
    <h1 class="text-2xl font-bold text-gray-900">Panel de Competición Local</h1>
    <p class="text-gray-600">Bienvenido, {{ Auth::user()->name }}</p>
</div>
@endsection
