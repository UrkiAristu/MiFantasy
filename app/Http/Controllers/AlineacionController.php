<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Jornada;
use App\Models\Liguilla;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlineacionController extends Controller
{
    public function guardarAlineacion(Request $request, $liguillaId)
    {
        try {
            $validated = $request->validate([
                'jornada_id' => 'required|exists:jornadas,id',
                'jugadores' => 'array',
                'jugadores.*' => 'exists:jugadores,id'
            ]);
            $usuarioId = Auth::id();

            // Comprobar que la liguilla existe (y, si quieres, que el user pertenece a ella)
            $liguilla = Liguilla::with('torneo')->findOrFail($liguillaId);

            // (Opcional pero recomendable) asegurar que el usuario está en la liguilla
            if (! $liguilla->usuarios()->where('users.id', $usuarioId)->exists()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No puedes modificar alineaciones de una liguilla en la que no participas.',
                ], 403);
            }
            // Buscar la jornada y verificar si ya ha comenzado
            $jornada = Jornada::with('partidos')->findOrFail($validated['jornada_id']);

            // Primer partido de la jornada
            $primerPartido = $jornada->partidos()->orderBy('fecha_partido')->first();

            if ($primerPartido && now()->gte($primerPartido->fecha_partido)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No puedes modificar la alineación, la jornada ya ha comenzado.'
                ], 403);
            }

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

            // Buscar alineación existente
            $alineacion = Alineacion::firstOrCreate(
                [
                    'user_id' => $usuarioId,
                    'liguilla_id' => $liguillaId,
                    'jornada_id' => $validated['jornada_id']
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
                'jugadores' => []
            ]);
        }
        $idTorneo = $alineacion->jornada->torneo->id;

        return response()->json([
            'status' => 'ok',
            'jugadores' => $alineacion->jugadores->map(function ($jugador) use ($idTorneo) {
                return [
                    'id' => $jugador->id,
                    'nombre' => $jugador->nombre,
                    'apellido1' => $jugador->apellido1,
                    'apellido2' => $jugador->apellido2,
                    'foto' => $jugador->foto ? asset($jugador->foto) : asset('assets/media/images/default-player.png'),
                ];
            }),
        ]);
    }
}
