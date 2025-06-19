<!DOCTYPE html>
<html lang="es">

<head>
    <title>MiFantasy - Registro</title>
    <meta charset="utf-8" />
    <meta name="description" content="Regístrate en MiFantasy, crea tu equipo y compite en torneos fantasy." />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600,700" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/fantasy.css') }}" rel="stylesheet" />
</head>

<body class="bg-body" style="background-color: #f4f4f4;">

    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">

            <!-- Imagen lateral -->
            <div class="d-flex flex-column flex-lg-row-auto w-lg-800px"
                style="background-image: url('/assets/media/images/login-fantasy.jpg'); background-size: cover; background-position: center;">
                <div class="d-lg-none text-center w-100 p-5 bg-dark bg-opacity-75">
                    <img src="/assets/media/logos/logo-fantasy.png" class="w-50" alt="Logo Fantasy">
                </div>
            </div>

            <!-- Formulario -->
            <div class="d-flex flex-column flex-lg-row-fluid py-10">
                <div class="d-flex flex-center flex-column flex-column-fluid">
                    <div class="w-lg-600px p-10 p-lg-15 mx-auto card shadow-lg">

                        <form method="POST" action="{{ url('/registro') }}" class="form w-100" id="formularioRegistro">
                            @csrf

                            <div class="text-center mb-10">
                                <h1 class="text-dark mb-3">Registro en MiFantasy</h1>
                                <div class="text-gray-500 fw-bold fs-5">¿Ya tienes cuenta?
                                    <a href="{{ url('/login') }}" class="link-primary fw-bolder">Inicia sesión</a>
                                </div>
                            </div>

                            <div class="row mb-5">

                                <div class="col-md-12">
                                    <label class="form-label">Nombre de usuario</label>
                                    <input type="text" name="nombreUsuario" class="form-control form-control-lg form-control-solid" required>
                                </div>
                            </div>

                            <div class="mb-5">
                                <label class="form-label">Correo electrónico</label>
                                <input type="email" name="email" class="form-control form-control-lg form-control-solid" required>
                            </div>

                            <div class="mb-5">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="password" id="password" class="form-control form-control-lg form-control-solid" required>
                            </div>

                            <div class="mb-5">
                                <label class="form-label">Confirmar contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-lg form-control-solid" required>
                            </div>

                            <div class="form-check mb-5">
                                <input type="checkbox" class="form-check-input" id="mostrar_contraseña" onclick="togglePassword()">
                                <label class="form-check-label" for="mostrar_contraseña">Mostrar contraseñas</label>
                            </div>

                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

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
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

    <script>
        function togglePassword() {
            const fields = ['password', 'password_confirmation'];
            fields.forEach(id => {
                const input = document.getElementById(id);
                input.type = input.type === 'password' ? 'text' : 'password';
            });
        }

        document.getElementById('formularioRegistro').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;

            if (password !== confirm) {
                e.preventDefault(); // Evita el envío del formulario
                alert('Las contraseñas no coinciden.');
            }
        });
    </script>
</body>

</html>