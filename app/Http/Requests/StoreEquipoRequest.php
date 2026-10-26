<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreEquipoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'torneos' => 'required|array|min:1',
            'torneos.*' => 'exists:torneos,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = Auth::user();
            if ($user && ! $user->admin) {
                $torneosGestionadosIds = $user->torneosGestionados()->pluck('torneos.id')->toArray();
                $solicitados = $this->input('torneos', []);
                foreach ($solicitados as $torneoId) {
                    if (! in_array($torneoId, $torneosGestionadosIds)) {
                        $validator->errors()->add('torneos', 'No tienes permisos para inscribir equipos en uno de los torneos seleccionados.');
                        break;
                    }
                }
            }
        });
    }
}
