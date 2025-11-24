<style>
    .menuMovil {
        background-color: #111;
        /* Fondo del menú */
        border-radius: 50px;
    }

    #menuMovil lord-icon,
    #menuMovil img {
        width: 32px;
        height: 32px;
    }
    
    #menuMovil svg {
        width: 24px;
        height: 24px;
    }
</style>
<div id="menuMovil"
    class="d-md-none fixed-bottom w-100 bg-transparent d-flex justify-content-center">

    <div class="menuMovil d-flex flex-wrap rounded-pill overflow-hidden shadow-lg"
        style="width: 80%; background-color: rgba(34, 34, 34, 0.9);">

        <!-- Botón atrás -->
        <div class="col-2 text-center pb-1"
            style="border-right: 2px solid white;"
            onclick="history.back()">
            <i class="bi bi-arrow-left text-white fs-4"></i>
        </div>

        <!-- Usuarios-->
        <div class="col-2 text-center pb-1"
            style="border-right: 2px solid white;">
            <a href="{{ url('/admin/usuarios') }}">
                <i class="bi bi-person-circle text-white fs-4"></i>
            </a>
        </div>


        <!-- Liguillas -->
        <div class="col-2 text-center pb-1"
            style="border-right: 2px solid white;">
            <a href="{{ url('/admin/liguillas') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                    <path fill="#ffffff" d="M353.8 118.1L330.2 70.3C326.3 62 314.1 61.7 309.8 70.3L286.2 118.1L233.9 125.6C224.6 127 220.6 138.5 227.5 145.4L265.5 182.4L256.5 234.5C255.1 243.8 264.7 251 273.3 246.7L320.2 221.9L366.8 246.3C375.4 250.6 385.1 243.4 383.6 234.1L374.6 182L412.6 145.4C419.4 138.6 415.5 127.1 406.2 125.6L353.9 118.1zM288 320C261.5 320 240 341.5 240 368L240 528C240 554.5 261.5 576 288 576L352 576C378.5 576 400 554.5 400 528L400 368C400 341.5 378.5 320 352 320L288 320zM80 384C53.5 384 32 405.5 32 432L32 528C32 554.5 53.5 576 80 576L144 576C170.5 576 192 554.5 192 528L192 432C192 405.5 170.5 384 144 384L80 384zM448 496L448 528C448 554.5 469.5 576 496 576L560 576C586.5 576 608 554.5 608 528L608 496C608 469.5 586.5 448 560 448L496 448C469.5 448 448 469.5 448 496z" />
                </svg>
            </a>
        </div>

        <!-- Torneos-->
        <div class="col-2 text-center pb-1"
            style="border-right: 2px solid white;">
            <a href="{{ url('/admin/torneos') }}">
                <i class="bi bi-trophy-fill text-white fs-4"></i>
            </a>
        </div>

        <!-- Equipos-->
        <div class="col-2 text-center pb-1"
            style="border-right: 2px solid white;">
            <a href="{{ url('/admin/equipos') }}">
                <i class="bi bi-shield-fill text-white fs-4"></i>
            </a>
        </div>

        <!-- Jugadores -->
        <div class="col-2 text-center pb-1">
            <a href="{{ url('/admin/jugadores') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                    <path fill="#ffffff" d="M376 88C376 57.1 350.9 32 320 32C289.1 32 264 57.1 264 88C264 118.9 289.1 144 320 144C350.9 144 376 118.9 376 88zM400 300.7L446.3 363.1C456.8 377.3 476.9 380.3 491.1 369.7C505.3 359.1 508.3 339.1 497.7 324.9L427.2 229.9C402 196 362.3 176 320 176C277.7 176 238 196 212.8 229.9L142.3 324.9C131.8 339.1 134.7 359.1 148.9 369.7C163.1 380.3 183.1 377.3 193.7 363.1L240 300.7L240 576C240 593.7 254.3 608 272 608C289.7 608 304 593.7 304 576L304 416C304 407.2 311.2 400 320 400C328.8 400 336 407.2 336 416L336 576C336 593.7 350.3 608 368 608C385.7 608 400 593.7 400 576L400 300.7z" />
                </svg> </a>
        </div>

    </div>
</div>