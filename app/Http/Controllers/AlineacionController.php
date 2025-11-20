<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Jornada;
use App\Models\Liguilla;
use App\Models\Plantilla;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlineacionController extends Controller
{
    public function guardarAlineacion(Request $request, $liguillaId)
    {
        try {
            $usuarioId = Auth::id();

            $validated = $request->validate([
                'jugadores' => 'array',
                'jugadores.*' => 'exists:jugadores,id'
            ]);

            // Comprobar que la liguilla existe (y, si quieres, que el user pertenece a ella)
            $liguilla = Liguilla::with('torneo')->findOrFail($liguillaId);

            // (Opcional pero recomendable) asegurar que el usuario está en la liguilla
            if (! $liguilla->usuarios()->where('users.id', $usuarioId)->exists()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No puedes modificar alineaciones de una liguilla en la que no participas.',
                ], 403);
            }
            //Obtener plantilla del usuario para comprobar que los jugadores son suyos
            $plantilla = Plantilla::with('jugadores')
                ->where('liguilla_id', $liguillaId)
                ->where('user_id', $usuarioId)
                ->first();

            // Evitamos duplicados por seguridad
            $jugadoresUnicos = array_unique($validated['jugadores'] ?? []);

            // Limitar al número permitido por torneo
            $maxJugadores = $liguilla->torneo->jugadores_por_equipo;
            if (count($jugadoresUnicos) > $maxJugadores) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Solo puedes seleccionar hasta $maxJugadores jugadores."
                ], 422);
            }
            // Comprobar que los jugadores están en la plantilla del usuario
            if ($plantilla) {
                $idsEnPlantilla = $plantilla->jugadores->pluck('id')->toArray();
                foreach ($jugadoresUnicos as $idJug) {
                    if (!in_array($idJug, $idsEnPlantilla)) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => 'Solo puedes alinear jugadores que están en tu plantilla.',
                        ], 422);
                    }
                }
            }


            // Buscar alineación BASE
            $alineacion = Alineacion::firstOrCreate(
                [
                    'user_id' => $usuarioId,
                    'liguilla_id' => $liguillaId,
                    'jornada_id' => null
                ]
            );

            // Sincronizar jugadores (evita duplicados automáticamente)
            $alineacion->jugadores()->sync($jugadoresUnicos);

            return response()->json([
                'status' => 'success',
                'message' => 'Alineación guardada correctamente'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al guardar la alineación: ' . $e->getMessage()
            ], 500);
        }
    }
    public function obtenerAlineacion($idLiguilla, $idJornada)
    {
        $user_id = Auth::id();

        $alineacion = Alineacion::with(['jugadores','jornada.torneo'])
            ->where('user_id', $user_id)
            ->where('liguilla_id', $idLiguilla)
            ->where('jornada_id', $idJornada)
            ->first();
        if (!$alineacion) {
            return response()->json([
                'status' => 'empty',
                'jugadores' => [],
                'total_puntos' => 0,
            ]);
        }
        $totalPuntos = $alineacion->jugadores->sum(fn($j) => $j->pivot->puntos ?? 0);
        return response()->json([
            'status' => 'ok',
            'jugadores' => $alineacion->jugadores->map(function ($jugador) {
                return [
                    'id' => $jugador->id,
                    'nombre' => $jugador->nombre,
                    'apellido1' => $jugador->apellido1,
                    'apellido2' => $jugador->apellido2,
                    'foto' => $jugador->foto ? asset($jugador->foto) : asset('assets/media/images/default-player.png'),
                ];
            })->values(),
            'total_puntos' => $totalPuntos,
        ]);
    }
}
