<!DOCTYPE html>
<html lang="es">

<head>
    <title>MiFantasy - Registro</title>
    <meta charset="utf-8" />
    <meta name="description" content="Regístrate en MiFantasy, crea tu equipo y compite en torneos fantasy." />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('assets/media/logos/logo-fantasy-nobg.png') }}" type="image/png">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600,700" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/fantasy.css') }}" rel="stylesheet" />
</head>

<body class="bg-body" style="background-color: #f4f4f4;">

    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">

            <!-- Imagen lateral -->
            <div class="d-flex flex-column flex-lg-row-auto w-lg-900px"
                style="background-image: url('/assets/media/images/login-fantasy.jpg'); background-size: cover; background-position: center;">
                <div class="d-lg-none text-center w-100 p-5 bg-dark bg-opacity-75">
                    <img src="/assets/media/logos/logo-fantasy.png" class="w-50" alt="Logo Fantasy">
                </div>
            </div>

            <!-- Formulario -->
            <div class="d-flex flex-column flex-lg-row-fluid py-8">
                <div class="d-flex flex-center flex-column flex-column-fluid">
                    <div class="w-lg-600px p-5 p-lg-10 mx-auto card shadow-lg">

                        <form class="form w-100" method="POST" action="{{ url('/registro') }}" id="formularioRegistro">
                            @csrf
                            <div class="text-center mb-10">
                                <h1 class="text-dark mb-3">Registro en MiFantasy</h1>
                                <div class="text-gray-500 fw-bold fs-5">¿Ya tienes cuenta?
                                    <a href="{{ url('/login') }}" class="link-primary fw-bolder">Inicia sesión</a>
                                </div>
                            </div>

                            <div class="fv-row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label fs-6 fw-bolder text-dark" for="nombreUsuario">Nombre de usuario</label>
                                    <input
                                        type="text"
                                        id="nombreUsuario"
                                        name="nombreUsuario"
                                        class="form-control form-control-lg form-control-solid 
                                        {{ $errors->has('nombreUsuario') ? 'is-invalid' : (old('nombreUsuario') ? 'is-valid' : '') }}"
                                        value="{{ old('nombreUsuario') }}"
                                        autofocus required>
                                    @if ($errors->has('nombreUsuario'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('nombreUsuario') }}
                                    </div>
                                    @elseif (old('nombreUsuario'))
                                    <div class="valid-feedback">
                                        ¡Genial! Nombre válido.
                                    </div>
                                    @else
                                    <div class="invalid-feedback">
                                        Por favor, introduce un nombre de usuario.
                                    </div>
                                    @endif
                                </div>
                            </div>


                            <div class="fv-row mb-3">
                                <label class="form-label fs-6 fw-bolder text-dark" for="email">Correo electrónico</label>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control form-control-lg form-control-solid {{ $errors->has('email') ? 'is-invalid' : (old('email') ? 'is-valid' : '') }}"
                                    required
                                    value="{{ old('email') }}">
                                @if ($errors->has('email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                                @elseif (old('email'))
                                <div class="valid-feedback">
                                    ¡Genial! Email válido.
                                </div>
                                @else
                                <div class="invalid-feedback">
                                    Por favor, introduce un correo electrónico válido.
                                </div>
                                @endif
                            </div>


                            <div class="fv-row mb-3">
                                <label class="form-label fs-6 fw-bolder text-dark" for="password">Contraseña</label>
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control form-control-lg form-control-solid {{ $errors->has('password') ? 'is-invalid' : (old('password') ? 'is-valid' : '') }}"
                                    required
                                    autocomplete="new-password">
                                @if ($errors->has('password'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('password') }}
                                </div>
                                @elseif (old('password'))
                                <div class="valid-feedback">
                                    Contraseña válida.
                                </div>
                                @else
                                <div class="invalid-feedback">
                                    Por favor, introduce una contraseña.
                                </div>
                                @endif
                            </div>

                            <div class="fv-row mb-3">
                                <label class="form-label fs-6 fw-bolder text-dark" for="password_confirmation">Confirmar contraseña</label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    class="form-control form-control-lg form-control-solid "
                                    required
                                    autocomplete="new-password">
                            </div>


                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="mostrar_contraseña" onclick="togglePassword()">
                                <label class="form-check-label " for="mostrar_contraseña">Mostrar contraseñas</label>
                            </div>

                            <!-- Mensajes -->
                            <!-- @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                                @endforeach
                            </div>
                            @endif -->

                            <!-- <div class="form-check mb-5">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">Acepto los <a href="#">términos y condiciones</a></label>
                            </div> -->

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>

    <script>
        function togglePassword() {
            const fields = ['password', 'password_confirmation'];
            fields.forEach(id => {
                const input = document.getElementById(id);
                input.type = input.type === 'password' ? 'text' : 'password';
            });
        }
    </script>
</body>

</html>