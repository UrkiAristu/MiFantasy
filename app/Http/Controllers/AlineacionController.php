<?php

namespace App\Http\Controllers;

use App\Actions\Alineacion\GuardarAlineacionAction;
use App\Models\Alineacion;
use App\Models\Jornada;
use App\Models\Jugador;
use App\Models\Liguilla;
use App\Models\Plantilla;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AlineacionController extends Controller
{
    public static array $formacionesPorModalidad = [
        '11' => [
            '4-4-2' => ['Portero' => 1, 'Defensa' => 4, 'Centrocampista' => 4, 'Delantero' => 2],
            '4-3-3' => ['Portero' => 1, 'Defensa' => 4, 'Centrocampista' => 3, 'Delantero' => 3],
            '3-5-2' => ['Portero' => 1, 'Defensa' => 3, 'Centrocampista' => 5, 'Delantero' => 2],
            '5-3-2' => ['Portero' => 1, 'Defensa' => 5, 'Centrocampista' => 3, 'Delantero' => 2],
            '3-4-3' => ['Portero' => 1, 'Defensa' => 3, 'Centrocampista' => 4, 'Delantero' => 3],
        ],
        '7' => [
            '3-2-1' => ['Portero' => 1, 'Defensa' => 3, 'Centrocampista' => 2, 'Delantero' => 1],
            '2-3-1' => ['Portero' => 1, 'Defensa' => 2, 'Centrocampista' => 3, 'Delantero' => 1],
            '2-2-2' => ['Portero' => 1, 'Defensa' => 2, 'Centrocampista' => 2, 'Delantero' => 2],
            '3-1-2' => ['Portero' => 1, 'Defensa' => 3, 'Centrocampista' => 1, 'Delantero' => 2],
        ],
        'sala' => [
            '1-2-1' => ['Portero' => 1, 'Defensa' => 1, 'Centrocampista' => 2, 'Delantero' => 1],
            '2-2'   => ['Portero' => 1, 'Defensa' => 2, 'Centrocampista' => 0, 'Delantero' => 2],
            '1-1-2' => ['Portero' => 1, 'Defensa' => 1, 'Centrocampista' => 1, 'Delantero' => 2],
            '2-1-1' => ['Portero' => 1, 'Defensa' => 2, 'Centrocampista' => 1, 'Delantero' => 1],
        ],
    ];

    public static function obtenerFormacionesPorModalidad(string $modalidad): array
    {
        return self::$formacionesPorModalidad[$modalidad] ?? self::$formacionesPorModalidad['sala'];
    }

    public function guardarAlineacion(Request $request, $liguillaId, GuardarAlineacionAction $action)
    {
        try {
            $usuarioId = Auth::id();

            $validated = $request->validate([
                'jugadores'   => 'nullable|array',
                'jugadores.*' => 'exists:jugadores,id',
                'formacion'   => 'nullable|string',
            ]);

            $result = $action->execute(
                usuarioId: (int) $usuarioId,
                liguillaId: (int) $liguillaId,
                jugadores: $validated['jugadores'] ?? [],
                formacion: $validated['formacion'] ?? null
            );

            if (! $result['success']) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $result['message'],
                ], $result['status_code'] ?? 422);
            }

            return response()->json([
                'status'    => 'success',
                'message'   => 'Alineación guardada correctamente',
                'formacion' => $result['formacion'],
            ]);
        } catch (Exception $e) {
            Log::error('Error al guardar alineación: ' . $e->getMessage(), [
                'user_id'     => Auth::id(),
                'liguilla_id' => $liguillaId,
                'trace'       => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Ocurrió un error inesperado al guardar la alineación.',
            ], 500);
        }
    }

    public function obtenerAlineacion($idLiguilla, $idJornada)
    {
        $user_id = Auth::id();

        $alineacion = Alineacion::with(['jugadores', 'jornada.torneo'])
            ->where('user_id', $user_id)
            ->where('liguilla_id', $idLiguilla)
            ->where('jornada_id', $idJornada)
            ->first();

        if (!$alineacion) {
            return response()->json([
                'status'       => 'empty',
                'formacion'    => null,
                'jugadores'    => [],
                'total_puntos' => 0,
            ]);
        }

        $totalPuntos = $alineacion->jugadores->sum(fn($j) => $j->pivot->puntos ?? 0);

        return response()->json([
            'status'       => 'ok',
            'formacion'    => $alineacion->formacion,
            'jugadores'    => $alineacion->jugadores->map(function ($jugador) {
                return [
                    'id'        => $jugador->id,
                    'nombre'    => $jugador->nombre,
                    'apellido1' => $jugador->apellido1,
                    'apellido2' => $jugador->apellido2,
                    'posicion'  => $jugador->posicion,
                    'foto'      => $jugador->foto ? asset($jugador->foto) : asset('assets/media/images/default-player.png'),
                    'puntos'    => $jugador->pivot->puntos ?? 0,
                ];
            })->values(),
            'total_puntos' => $totalPuntos,
        ]);
    }
}
