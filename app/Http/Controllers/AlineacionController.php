<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Jornada;
use App\Models\Liguilla;
use Exception;
use Illuminate\Http\Request;

class AlineacionController extends Controller
{
    public function guardarAlineacion(Request $request, $liguillaId)
    {
        try {
            $request->validate([
                'jornada_id' => 'required|exists:jornadas,id',
                'jugadores' => 'array',
                'jugadores.*' => 'exists:jugadores,id'
            ]);
            $usuarioId = session('cuenta');
            // Buscar la jornada y verificar si ya ha comenzado
            $jornada = Jornada::with('partidos')->findOrFail($request->jornada_id);

            // Primer partido de la jornada
            $primerPartido = $jornada->partidos()->orderBy('fecha_partido')->first();

            if ($primerPartido && now()->gte($primerPartido->fecha_partido)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No puedes modificar la alineación, la jornada ya ha comenzado.'
                ], 403);
            }

            // Evitamos duplicados por seguridad
            $jugadoresUnicos = array_unique($request->jugadores ?? []);

            // Limitar al número permitido por torneo
            $liguilla = Liguilla::findOrFail($liguillaId);
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
                    'cuenta_id' => $usuarioId,
                    'liguilla_id' => $liguillaId,
                    'jornada_id' => $request->jornada_id
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
        $cuenta_id = session('cuenta');
        $alineacion = Alineacion::where('cuenta_id', $cuenta_id)
            ->where('liguilla_id', $idLiguilla)
            ->where('jornada_id', $idJornada)
            ->with('jugadores')
            ->first();;
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
