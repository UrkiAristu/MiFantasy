@extends('user.layouts.app')

@section('title', 'Perfil')

@section('content')
<div class="container my-4">

    <h1 class="h3 mb-4">Mi Perfil</h1>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">

            <div id="actualizar-perfil" class="card mb-4 shadow-sm">
                <div class="card-body">

                    <h5 class="card-title mb-2">Información del perfil</h5>
                    <p class="text-muted small mb-3">Actualiza tus datos personales y tu correo electrónico.</p>
                    {{-- Form verificación email --}}
                    <form id="send-verification" method="post" action="{{ route('user.verification.send') }}">
                        @csrf
                    </form>
                    {{-- Form actualizar perfil --}}
                    <form method="post" action="{{ url('/user/perfil/actualizar') }}">
                        @csrf

                        {{-- Nombre --}}
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}"
                                required>

                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email"
                                name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}"
                                required>

                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if (session('success'))
                        <span class="alert alert-success py-2 px-3 mt-2 mb-0 small d-flex align-items-center gap-2">
                            {{ session('success') }}
                        </span>
                        @endif
                        {{-- Estado de verificación del email --}}
                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail)
                        @if (! $user->hasVerifiedEmail())
                        <div class="mt-2 mb-5">
                            <span class="d-inline-flex align-items-center gap-1 small">
                                <i class="bi bi-x-circle-fill text-danger me-1"></i>
                                <span class="text-dark">Email no verificado.</span>
                            </span>
                            @if (session('status') === 'verification-link-sent')
                            <span class="alert alert-success py-2 px-3 mt-2 mb-0 small d-flex align-items-center gap-2">
                                Se ha enviado un nuevo enlace de verificación al correo electrónico.
                            </span>
                            @endif
                        </div>
                        @else
                        <div class="mt-2 mb-5">
                            <span>
                                <i class="bi bi-check-circle-fill me-1 text-success"></i>
                                Email verificado
                            </span>
                        </div>
                        @endif
                        @endif

                        <div class="d-flex flex-column flex-md-row align-items-start gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">
                                Guardar cambios
                            </button>
                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <button type="button" id="btn-send-verification" class="btn btn-secondary">
                                Enviar email de verificación
                            </button>
                            @endif
                        </div>
                    </form>

                </div>
            </div>

            <div id="actualizar-password" class="card mb-4 shadow-sm">
                <div class="card-body">

                    <h5 class="card-title mb-2">Cambiar contraseña</h5>
                    <p class="text-muted small mb-3">Asegúrate de usar una contraseña larga y segura.</p>

                    <form method="post" action="{{url('/user/perfil/password')}}">
                        @csrf

                        {{-- Contraseña actual --}}
                        <div class="mb-3">
                            <label class="form-label">Contraseña actual</label>
                            <input type="password"
                                name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                required>

                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nueva contraseña --}}
                        <div class="mb-3">
                            <label class="form-label">Nueva contraseña</label>
                            <input type="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required>

                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Confirmación --}}
                        <div class="mb-3">
                            <label class="form-label">Confirmar contraseña</label>
                            <input type="password"
                                name="password_confirmation"
                                class="form-control"
                                required>
                        </div>

                        <button type="submit" class="btn btn-primary">Actualizar contraseña</button>

                        @if (session('password_success'))
                        <div class="alert alert-success py-2 px-3 mt-2 mb-0 small d-inline-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill"></i>
                            {{ session('password_success') }}
                        </div>
                        @endif
                    </form>
                </div>
            </div>

            <div id="eliminar-perfil" class="card mb-4 shadow-sm">
                <div class="card-body">

                    <h5 class="card-title mb-2 text-danger">Eliminar cuenta</h5>
                    <p class="text-muted small mb-3">
                        Una vez elimines tu cuenta, no podrás recuperarla. Esta acción es permanente.
                    </p>

                    @if (session('delete_error'))
                    <div class="alert alert-danger mt-3 py-2 px-3 small d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ session('delete_error') }}
                    </div>
                    @endif

                    {{-- Botón abrir modal --}}
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        Eliminar cuenta
                    </button>

                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="{{ url('/user/perfil/eliminar') }}" class="modal-content">
            @csrf
            @method('delete')

            <div class="modal-header">
                <h5 class="modal-title text-danger">Eliminar cuenta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <p>Introduce tu contraseña para confirmar esta acción.</p>

                <input type="password"
                    name="password_deletion"
                    class="form-control @error('password_deletion', 'userDeletion') is-invalid @enderror"
                    placeholder="Contraseña"
                    required>

                @error('password_deletion', 'userDeletion')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger">Eliminar definitivamente</button>
            </div>
        </form>
    </div>
</div>

@endsection
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const btn = document.getElementById("btn-send-verification");
        const form = document.getElementById("send-verification");

        if (btn) {
            btn.addEventListener("click", function() {

                Swal.fire({
                    title: "¿Enviar email de verificación?",
                    text: "Te enviaremos un nuevo enlace a tu correo electrónico.",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Sí, enviar",
                    cancelButtonText: "Cancelar",
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });

            });
        }

    });
</script>
@endpush