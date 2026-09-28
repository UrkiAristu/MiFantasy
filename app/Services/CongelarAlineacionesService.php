<?php

namespace App\Services;

use App\Jobs\CongelarJornadaJob;
use App\Models\Jornada;

class CongelarAlineacionesService
{
    /**
     * Despacha el Job asíncrono para congelar las alineaciones de la jornada.
     */
    public function congelarJornada(Jornada $jornada): void
    {
        CongelarJornadaJob::dispatch($jornada);
    }
}
