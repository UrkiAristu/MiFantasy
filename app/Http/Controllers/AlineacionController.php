<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Jornada;
use App\Models\Jugador;
use App\Models\Liguilla;
use App\Models\Plantilla;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function guardarAlineacion(Request $request, $liguillaId)
    {
        try {
            $usuarioId = Auth::id();

            $validated = $request->validate([
                'jugadores'   => 'nullable|array',
                'jugadores.*' => 'exists:jugadores,id',
                'formacion'   => 'nullable|string',
            ]);

            // Comprobar que la liguilla existe y cargar el torneo
            $liguilla = Liguilla::with('torneo')->findOrFail($liguillaId);

            // Asegurar que el usuario pertenece a la liguilla
            if (! $liguilla->usuarios()->where('users.id', $usuarioId)->exists()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No puedes modificar alineaciones de una liguilla en la que no participas.',
                ], 403);
            }

            $modalidad = (string) ($liguilla->torneo->modalidad ?? 'sala');
            $formacionesDisponibles = self::obtenerFormacionesPorModalidad($modalidad);

            // Validar formación
            if (!empty($validated['formacion'])) {
                if (!isset($formacionesDisponibles[$validated['formacion']])) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => "La formación '{$validated['formacion']}' no es válida para la modalidad '$modalidad'.",
                    ], 422);
                }
                $formacionElegida = $validated['formacion'];
            } else {
                $alineacionExistente = Alineacion::where('user_id', $usuarioId)
                    ->where('liguilla_id', $liguillaId)
                    ->whereNull('jornada_id')
                    ->first();
                $formacionElegida = ($alineacionExistente && isset($formacionesDisponibles[$alineacionExistente->formacion]))
                    ? $alineacionExistente->formacion
                    : array_key_first($formacionesDisponibles);
            }

            $cuotas = $formacionesDisponibles[$formacionElegida];
            $maxJugadores = array_sum($cuotas);

            // Evitamos duplicados
            $jugadoresUnicos = array_values(array_unique($validated['jugadores'] ?? []));

            // Limitar al número total permitido por la formación
            if (count($jugadoresUnicos) > $maxJugadores) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Solo puedes seleccionar hasta $maxJugadores jugadores para la formación $formacionElegida.",
                ], 422);
            }

            // Obtener plantilla del usuario para comprobar que los jugadores son suyos
            $plantilla = Plantilla::with('jugadores')
                ->where('liguilla_id', $liguillaId)
                ->where('user_id', $usuarioId)
                ->first();

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

            // Validar topes por posición según la formación
            if (!empty($jugadoresUnicos)) {
                $jugadoresModelos = Jugador::whereIn('id', $jugadoresUnicos)->get();
                $conteoPorPosicion = [
                    'Portero'        => 0,
                    'Defensa'        => 0,
                    'Centrocampista' => 0,
                    'Delantero'      => 0,
                ];

                foreach ($jugadoresModelos as $jugador) {
                    $pos = $jugador->posicion;
                    if ($pos && isset($conteoPorPosicion[$pos])) {
                        $conteoPorPosicion[$pos]++;
                    }
                }

                foreach ($cuotas as $posicion => $limite) {
                    $actual = $conteoPorPosicion[$posicion] ?? 0;
                    if ($actual > $limite) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => "Has seleccionado $actual jugadores para la posición '$posicion', pero la formación $formacionElegida solo permite un máximo de $limite.",
                        ], 422);
                    }
                }
            }

            // Buscar o crear alineación BASE
            $alineacion = Alineacion::firstOrCreate([
                'user_id'     => $usuarioId,
                'liguilla_id' => $liguillaId,
                'jornada_id'  => null,
            ]);

            $alineacion->formacion = $formacionElegida;
            $alineacion->save();

            // Sincronizar jugadores
            $alineacion->jugadores()->sync($jugadoresUnicos);

            return response()->json([
                'status'    => 'success',
                'message'   => 'Alineación guardada correctamente',
                'formacion' => $formacionElegida,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al guardar la alineación: ' . $e->getMessage(),
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
