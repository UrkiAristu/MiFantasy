@extends('admin.layouts.app')

@section('title', 'Inicio | Admin')

@section('content')
<div class="container">
    <h1 class="m-4">Panel de Administración</h1>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-person-circle fs-1 mb-3 text-dark"></i>
                    <h5 class="card-title">Usuarios</h5>
                    <p class="card-text">Gestiona los usuarios registrados en el sistema.</p>
                    <a href="{{ url('/admin/usuarios') }}" class="btn btn-primary w-100">Ver Usuarios</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="icon" style="width: 2em; height: 2em;">
                        <path d="M353.8 54.1L330.2 6.3c-3.9-8.3-16.1-8.6-20.4 0L286.2 54.1l-52.3 7.5c-9.3 1.4-13.3 12.9-6.4 19.8l38 37-9 52.1c-1.4 9.3 8.2 16.5 16.8 12.2l46.9-24.8 46.6 24.4c8.6 4.3 18.3-2.9 16.8-12.2l-9-52.1 38-36.6c6.8-6.8 2.9-18.3-6.4-19.8l-52.3-7.5zM256 256c-17.7 0-32 14.3-32 32l0 192c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-192c0-17.7-14.3-32-32-32l-128 0zM32 320c-17.7 0-32 14.3-32 32L0 480c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-128c0-17.7-14.3-32-32-32L32 320zm416 96l0 64c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-64c0-17.7-14.3-32-32-32l-128 0c-17.7 0-32 14.3-32 32z" />
                    </svg>
                    <h5 class="card-title">Liguillas</h5>
                    <p class="card-text">Revisa y administra las liguillas creadas.</p>
                    <a href="{{ url('/admin/liguillas') }}" class="btn btn-primary w-100">Ver Liguillas</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-trophy-fill fs-1 mb-3 text-dark"></i>
                    <h5 class="card-title">Torneos</h5>
                    <p class="card-text">Administra los torneos disponibles.</p>
                    <a href="{{ url('/admin/torneos') }}" class="btn btn-primary w-100">Ver Torneos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-shield-fill fs-1 mb-3 text-dark"></i>
                    <h5 class="card-title">Equipos</h5>
                    <p class="card-text">Controla los equipos participantes.</p>
                    <a href="{{ url('/admin/equipos') }}" class="btn btn-primary w-100">Ver Equipos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="icon" style="width: 2em; height: 2em;">
                        <path d="M112 48a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm40 304l0 128c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-223.1L59.4 304.5c-9.1 15.1-28.8 20-43.9 10.9s-20-28.8-10.9-43.9l58.3-97c17.4-28.9 48.6-46.6 82.3-46.6l29.7 0c33.7 0 64.9 17.7 82.3 46.6l58.3 97c9.1 15.1 4.2 34.8-10.9 43.9s-34.8 4.2-43.9-10.9L232 256.9 232 480c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-128-16 0z" />
                    </svg>
                    <h5 class="card-title">Jugadores</h5>
                    <p class="card-text">Administra los jugadores de cada equipo.</p>
                    <a href="{{ url('/admin/jugadores') }}" class="btn btn-primary w-100">Ver Jugadores</a>
                </div>
            </div>
        </div>
        @endsection