<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Models\Liguilla;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LiguillaController extends Controller
{
    public function crearLiguilla(Request $request)
    {
        // Validar los datos del formulario
        $validator = Validator::make(
            $request->all(),
            [
                'nombre' => 'required|string|max:255',
                'num_max_part' => 'required|integer|min:2|max:100',
                'torneo_id' => 'required|integer|exists:torneos,id'
            ],
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'nombre.string' => 'El nombre debe ser un string',
                'nombre.max' => 'El nombre debe tener un maximo de 255 caracteres',
                'num_max_part.required' => 'El número máximo de participantes es obligatorio.',
                'num_max_part.integer' => 'Debe ser un número entero.',
                'num_max_part.min' => 'Debe haber al menos 2 participantes.',
                'num_max_part.max' => 'No se permiten más de 100 participantes.',
                'torneo_id.required' => 'El torneo es obligatorio.',
                'torneo_id.integer' => 'El ID del torneo debe ser un número entero.',
                'torneo_id.exists' => 'El torneo seleccionado no existe.',
            ]
        );
        if ($validator->fails()) {
            return redirect('/user/torneos')
                ->withErrors($validator)
                ->withInput();
        }
        $torneo = Torneo::find($request->torneo_id);
        if ($torneo) {
            $liguilla = new Liguilla();
            $liguilla->nombre = $request->nombre;
            $liguilla->torneo_id = $torneo->id;
            $liguilla->max_usuarios = $request->num_max_part;
            $liguilla->creador_id = session('cuenta');
            $liguilla->codigo_unico = Str::random(8); // Código para unirse
            $liguilla->save();

            // Añadir al creador como primer usuario
            $liguilla->usuarios()->attach(session('cuenta'));
            // Redirigir a la página de torneos con un mensaje de éxito
            return redirect('/user/liguillas')->with('success', 'Ligulla creada correctamente.');
        } else {
            return redirect('/user/torneos')
                ->withErrors(['torneo' => 'Torneo no encontrado.'])
                ->withInput();
        }
    }
    public function mostrarPaginaLiguillasUser()
    {
        $usuario = Cuenta::find(session('cuenta'));
        if (!$usuario) {
            return redirect('/login')->withErrors('Usuario no encontrado.');
        }

        // Obtener las liguillas con el torneo y datos del pivot
        $liguillasUsuario = $usuario->liguillas()->with('torneo')->get();
        return view('user.liguillas', compact('liguillasUsuario'));
    }
    public function mostrarPaginaUnirseLiguillasUser()
    {
        return view('user.unirseLiguilla');
    }
    public function unirseLiguilla(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|size:8', // suponiendo código de 8 caracteres
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.size' => 'El código debe tener exactamente 8 caracteres.',
        ]);

        $codigo = strtoupper($request->input('codigo')); // uniformizar mayúsculas

        // Buscar liguilla por código único
        $liguilla = Liguilla::where('codigo_unico', $codigo)->first();

        if (!$liguilla) {
            return redirect()->back()->withErrors(['codigo' => 'Código de liguilla no válido.'])->withInput();
        }

        $usuarioId = session('cuenta');

        // Comprobar si el usuario ya está en esa liguilla
        if ($liguilla->usuarios()->where('usuario_id', $usuarioId)->exists()) {
            return redirect()->back()->withErrors(['codigo' => 'Ya estás inscrito en esta liguilla.'])->withInput();
        }

        // Comprobar si la liguilla está llena
        if ($liguilla->usuarios()->count() >= $liguilla->max_usuarios) {
            return redirect()->back()->withErrors(['codigo' => 'La liguilla ya está completa.'])->withInput();
        }

        // Añadir usuario a la liguilla
        $liguilla->usuarios()->attach($usuarioId);

        return redirect('/user/liguillas')->with('success', 'Te has unido correctamente a la liguilla.');
    }
}
