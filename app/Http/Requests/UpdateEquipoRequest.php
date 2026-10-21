<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateEquipoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $equipo = $this->route('equipo');
        return $equipo ? $this->user()->can('update', $equipo) : false;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'torneos' => 'nullable|array',
            'torneos.*' => 'exists:torneos,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = Auth::user();
            $equipo = $this->route('equipo');

            if ($user && !$user->admin && $equipo) {
                $torneosGestionadosIds = $user->torneosGestionados()->pluck('torneos.id')->toArray();

                $equipoTorneosIds = $equipo->torneos()->pluck('torneos.id')->toArray();
                if (empty(array_intersect($equipoTorneosIds, $torneosGestionadosIds))) {
                    $validator->errors()->add('base', 'No tienes permisos para modificar este equipo.');
                }

                $solicitados = $this->input('torneos', []);
                foreach ($solicitados as $torneoId) {
                    if (!in_array($torneoId, $torneosGestionadosIds)) {
                        $validator->errors()->add('torneos', 'No tienes permisos para asignar el equipo a uno de los torneos seleccionados.');
                        break;
                    }
                }
            }
        });
    }
}
