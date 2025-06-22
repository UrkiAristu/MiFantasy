@extends('admin.layouts.app')

@section('title', 'Inicio | Admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Panel de Administración</h1>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Usuarios</h5>
                    <p class="card-text">Gestiona los usuarios registrados en el sistema.</p>
                    <a href="{{ url('/admin/usuarios') }}" class="btn btn-primary w-100">Ver Usuarios</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Liguillas</h5>
                    <p class="card-text">Revisa y administra las liguillas creadas.</p>
                    <a href="{{ url('/admin/liguillas') }}" class="btn btn-primary w-100">Ver Liguillas</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Equipos</h5>
                    <p class="card-text">Controla los equipos participantes.</p>
                    <a href="{{ url('/admin/equipos') }}" class="btn btn-primary w-100">Ver Equipos</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection