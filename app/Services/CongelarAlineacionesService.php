<?php

namespace App\Services;

use App\Models\Alineacion;
use App\Models\Jornada;
use App\Models\Liguilla;
use Illuminate\Support\Facades\DB;

class CongelarAlineacionesService
{
    /**
     * Congela las alineaciones de TODAS las liguillas del torneo de esta jornada.
     */
    public function congelarJornada(Jornada $jornada): void
    {
        $torneo = $jornada->torneo;

        if (!$torneo) {
            return;
        }

        $liguillas = $torneo->liguillas; // todas las liguillas del torneo

        foreach ($liguillas as $liguilla) {
            $this->congelarJornadaEnLiguilla($jornada, $liguilla);
        }

        $jornada->alineaciones_congeladas = true;
        $jornada->save();
    }

    /**
     * Congela las alineaciones para una sola liguilla.
     */
    protected function congelarJornadaEnLiguilla(Jornada $jornada, Liguilla $liguilla): void
    {
        DB::transaction(function () use ($liguilla, $jornada) {

            foreach ($liguilla->usuarios as $user) {

                // Ya existe "foto" → saltar
                $yaExiste = Alineacion::where('liguilla_id', $liguilla->id)
                    ->where('user_id', $user->id)
                    ->where('jornada_id', $jornada->id)
                    ->exists();

                if ($yaExiste) {
                    continue;
                }

                // Obtener alineación base
                $alineacionBase = $liguilla->alineacionBaseDe($user);

                if (!$alineacionBase) {
                    continue; // el usuario no tiene base → sin alineación congelada
                }

                // Crear la alineación "foto"
                $alineacionJornada = Alineacion::create([
                    'user_id'     => $user->id,
                    'liguilla_id' => $liguilla->id,
                    'jornada_id'  => $jornada->id,
                ]);

                // Copiar jugadores tal cual están
                $syncData = [];
                foreach ($alineacionBase->jugadores as $jugador) {
                    $syncData[$jugador->id] = ['puntos' => 0];
                }

                $alineacionJornada->jugadores()->sync($syncData);
            }
        });
    }
}
