<?php

namespace App\Actions\Plantilla;

use App\Models\Jugador;
use App\Models\Liguilla;
use App\Models\Plantilla;
use Illuminate\Support\Facades\DB;

class GenerarPlantillaAleatoriaAction
{
    /**
     * Genera una plantilla aleatoria para un usuario en una liguilla.
     */
    public function execute(int $liguillaId, int $usuarioId): Plantilla
    {
        $plantilla = Plantilla::create([
            'liguilla_id' => $liguillaId,
            'user_id' => $usuarioId,
        ]);

        $liguilla = Liguilla::with('torneo')->findOrFail($liguillaId);
        $limite = $liguilla->torneo->jugadores_por_equipo + 3;

        // Seleccionar IDs de jugadores disponibles del torneo y barajar en memoria para evitar ORDER BY RAND()
        $jugadorIds = Jugador::whereHas('participaciones', function ($query) use ($liguilla) {
            $query->where('torneo_id', $liguilla->torneo->id);
        })
            ->whereDoesntHave('plantillas', function ($query) use ($liguilla) {
                $query->where('liguilla_id', $liguilla->id);
            })
            ->pluck('id')
            ->shuffle()
            ->take($limite);

        if ($jugadorIds->isNotEmpty()) {
            $now = now();
            $registros = $jugadorIds->map(function ($jugadorId) use ($plantilla, $now) {
                return [
                    'plantilla_id' => $plantilla->id,
                    'jugador_id' => $jugadorId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })->all();

            DB::table('jugador_plantilla')->insert($registros);
        }

        return $plantilla;
    }
}
