<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Fantasy League - Iniciar sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- SEO -->
    <meta name="description" content="Accede a tu cuenta para gestionar tu equipo de Fantasy League.">
    <meta name="keywords" content="fantasy, login, fútbol, liga, manager, fantasy league">

    <!-- Estilos -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:400,600,700">
    <link rel="stylesheet" href="/assets/plugins/global/plugins.bundle.css">
    <link rel="stylesheet" href="/assets/css/style.bundle.css">
    <link rel="stylesheet" href="/assets/css/fantasy.css">
</head>

<body class="bg-body d-flex flex-column flex-root">

    <div class="d-flex flex-column flex-lg-row flex-column-fluid">

        <!-- Lado visual -->
        <div class="d-flex flex-column flex-lg-row-auto w-lg-900px"
            style="background-image: url('/assets/media/images/login-fantasy.jpg'); background-size: cover; background-position: center;">
            <div class="d-lg-none text-center w-100 p-5 bg-dark bg-opacity-75">
                <img src="/assets/media/logos/logo-fantasy.png" class="w-50" alt="Logo Fantasy">
            </div>
        </div>

        <!-- Contenido -->
        <div class="d-flex flex-column flex-lg-row-fluid py-10">
            <div class="d-flex flex-center flex-column flex-column-fluid">
                <div class="w-lg-500px p-10 p-lg-15 mx-auto card shadow">

                    <!-- Formulario -->
                    <form class="form w-100" method="POST" action="/login">
                        @csrf

                        <!-- Encabezado -->
                        <div class="text-center mb-10">
                            <!-- <img src="/assets/media/logos/logo-fantasy.png" class="mb-5" style="width: 120px;" alt="Logo Fantasy"> -->
                            <h1 class="text-dark mb-3">Iniciar sesión</h1>
                            <div class="text-gray-500 fw-bold fs-6">¿Aún no tienes cuenta?
                                <a href="/registro" class="link-primary fw-bolder">Regístrate aquí</a>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="fv-row mb-10">
                            <label class="form-label fs-6 fw-bolder text-dark">Nombre de usuario/Correo electrónico</label>
                            <input type="text" name="nombreUsuario" class="form-control form-control-lg form-control-solid" required autofocus value="{{ old('nombreUsuario') }}">
                        </div>

                        <!-- Contraseña -->
                        <div class="fv-row mb-10">
                            <div class="d-flex justify-content-between mb-2">
                                <label class="form-label fs-6 fw-bolder text-dark mb-0">Contraseña</label>
                                <a href="/recuperar" class="link-primary fs-6 fw-bolder">¿Olvidaste tu contraseña?</a>
                            </div>
                            <input type="password" name="password" id="password" class="form-control form-control-lg form-control-solid" required value="{{ old('password') }}">
                            <div class="form-check mt-5">
                                <input type="checkbox" class="form-check-input" id="mostrar_contraseña" onclick="togglePassword()">
                                <label class="form-check-label" for="mostrar_contraseña">Mostrar contraseñas</label>
                            </div>
                        </div>



                        <!-- Mensajes -->
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                            @endforeach
                        </div>
                        @endif

                        <!-- Botón de login -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-lg btn-primary w-100">Entrar</button>
                        </div>
                    </form>
                    <!-- Fin formulario -->

                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/assets/plugins/global/plugins.bundle.js"></script>
    <script src="/assets/js/scripts.bundle.js"></script>

    <script>
        window.onload = function() {
            try {
                window.ReactNativeWebView?.postMessage(JSON.stringify({
                    accion: "dispId"
                }));
                window.ReactNativeWebView?.postMessage(JSON.stringify({
                    accion: "bioLogin"
                }));
            } catch (e) {}
        };
    </script>
    <script>
        //Funcion para alternar la visibilidad de la contraseña
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const isChecked = document.getElementById('mostrar_contraseña').checked;

            if (isChecked) {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        }
    </script>
</body>

</html>