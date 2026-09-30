<?php

namespace App\Actions\Alineacion;

use App\Http\Controllers\AlineacionController;
use App\Models\Alineacion;
use App\Models\Jugador;
use App\Models\Liguilla;
use App\Models\Plantilla;
use Exception;
use Illuminate\Validation\ValidationException;

class GuardarAlineacionAction
{
    /**
     * Valida y guarda la alineación base de un usuario en una liguilla.
     *
     * @param int $usuarioId
     * @param int $liguillaId
     * @param array $jugadores
     * @param string|null $formacion
     * @return array
     * @throws Exception
     */
    public function execute(int $usuarioId, int $liguillaId, array $jugadores = [], ?string $formacion = null): array
    {
        // Comprobar que la liguilla existe y cargar el torneo
        $liguilla = Liguilla::with('torneo')->findOrFail($liguillaId);

        // Asegurar que el usuario pertenece a la liguilla
        if (! $liguilla->usuarios()->where('users.id', $usuarioId)->exists()) {
            return [
                'success' => false,
                'status_code' => 403,
                'message' => 'No puedes modificar alineaciones de una liguilla en la que no participas.',
            ];
        }

        $modalidad = (string) ($liguilla->torneo->modalidad ?? 'sala');
        $formacionesDisponibles = AlineacionController::obtenerFormacionesPorModalidad($modalidad);

        // Validar formación
        if (!empty($formacion)) {
            if (!isset($formacionesDisponibles[$formacion])) {
                return [
                    'success' => false,
                    'status_code' => 422,
                    'message' => "La formación '{$formacion}' no es válida para la modalidad '$modalidad'.",
                ];
            }
            $formacionElegida = $formacion;
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
        $jugadoresUnicos = array_values(array_unique($jugadores));

        // Limitar al número total permitido por la formación
        if (count($jugadoresUnicos) > $maxJugadores) {
            return [
                'success' => false,
                'status_code' => 422,
                'message' => "Solo puedes seleccionar hasta $maxJugadores jugadores para la formación $formacionElegida.",
            ];
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
                    return [
                        'success' => false,
                        'status_code' => 422,
                        'message' => 'Solo puedes alinear jugadores que están en tu plantilla.',
                    ];
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
                    return [
                        'success' => false,
                        'status_code' => 422,
                        'message' => "Has seleccionado $actual jugadores para la posición '$posicion', pero la formación $formacionElegida solo permite un máximo de $limite.",
                    ];
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

        return [
            'success'   => true,
            'formacion' => $formacionElegida,
            'alineacion'=> $alineacion,
        ];
    }
}
