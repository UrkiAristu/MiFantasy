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
</style>
<div id="menuMovil"
    class="d-lg-none fixed-bottom w-100 bg-transparent mb-3 d-flex justify-content-center">

    <div class="menuMovil d-flex flex-wrap rounded-pill overflow-hidden shadow-lg"
        style="width: 80%; background-color: rgba(34, 34, 34, 0.9);">

        <!-- Botón atrás -->
        <div class="col-3 text-center p-1"
            style="border-right: 2px solid white;"
            onclick="history.back()">
            <i class="bi bi-arrow-left text-white fs-4"></i>
        </div>

        <!-- Mis ligas-->
        <div class="col-3 text-center p-1"
            style="border-right: 2px solid white;">
            <a href="{{ url('/user/liguillas') }}">
                <i class="bi bi-trophy text-white fs-4"></i>
            </a>
        </div>


        <!-- Crear Liga -->
        <div class="col-3 text-center p-1"
            style="border-right: 2px solid white;">
            <a href="{{ url('/user/torneos') }}">
                <i class="bi bi-plus-circle text-white fs-4"></i>
            </a>
        </div>


        <!-- Unirse a liga -->
        <div class="col-3 text-center p-1">
            <a href="{{ url('/user/unirseLiguilla') }}">
                <i class="bi bi-bookmark-plus text-white fs-4"></i>
            </a>
        </div>

    </div>
</div>