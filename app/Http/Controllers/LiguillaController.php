<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Cuenta;
use App\Models\Jugador;
use App\Models\Liguilla;
use App\Models\Plantilla;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            // Crear plantilla aleatoria para este usuario en la liguilla
            $this->crearPlantillaAleatoria($liguilla->id, session('cuenta'));
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
    public function mostrarPaginaUnirseLiguillasUser(Request $request)
    {
        $codigo = $request->query('codigo'); // o $request->input('codigo')
        return view('user.unirseLiguilla', compact('codigo'));
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
        if ($liguilla->usuarios()->where('cuenta_id', $usuarioId)->exists()) {
            return redirect()->back()->withErrors(['codigo' => 'Ya estás inscrito en esta liguilla.'])->withInput();
        }

        // Comprobar si la liguilla está llena
        if ($liguilla->usuarios()->count() >= $liguilla->max_usuarios) {
            return redirect()->back()->withErrors(['codigo' => 'La liguilla ya está completa.'])->withInput();
        }

        // Añadir usuario a la liguilla
        $liguilla->usuarios()->attach($usuarioId);
        // Crear plantilla aleatoria para este usuario en la liguilla
        $this->crearPlantillaAleatoria($liguilla->id, $usuarioId);

        return redirect('/user/liguillas')->with('success', 'Te has unido correctamente a la liguilla.');
    }
    private function crearPlantillaAleatoria($liguillaId, $usuarioId)
    {
        // Crear registro de plantilla
        $plantilla = Plantilla::create([
            'liguilla_id' => $liguillaId,
            'cuenta_id' => $usuarioId
        ]);
        $liguilla = Liguilla::findOrFail($liguillaId);

        // Seleccionar jugadores aleatorios del torneo de esa liguilla
        $jugadores = Jugador::whereHas('participaciones', function ($query) use ($liguilla) {
            $query->where('torneo_id', $liguilla->torneo->id);
        })
            ->whereDoesntHave('plantillas', function ($query) use ($liguilla) {
                $query->where('liguilla_id', $liguilla->id);
            })
            ->inRandomOrder()
            ->limit(value: $liguilla->torneo->jugadores_por_equipo + 3)
            ->get();
        foreach ($jugadores as $jugador) {
            DB::table('jugador_plantilla')->insert([
                'plantilla_id' => $plantilla->id,
                'jugador_id' => $jugador->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    public function mostrarPaginaLiguillaUser($id)
    {
        $usuarioId = session('cuenta'); // ID de usuario logueado
        $usuario = Cuenta::findOrFail($usuarioId);

        // 1️⃣ Liguilla y torneo
        $liguilla = Liguilla::with('torneo.jornadas.partidos')
            ->findOrFail($id);

        // 2️⃣ Clasificación general
        $clasificacion = $liguilla->usuarios()
            ->withPivot('puntos')
            ->orderByDesc('pivot_puntos')
            ->get()
            ->map(function ($usuario, $index) {
                return (object) [
                    'posicion' => $index + 1,
                    'nombre' => $usuario->nombreUsuario,
                    'email' => $usuario->email,
                    'puntos' => $usuario->pivot->puntos ?? 0
                ];
            });

        // 3️⃣ Jornada actual o próxima
        $hoy = now();
        $jornadaActiva = $liguilla->torneo->jornadas()
            ->whereDate('fecha_inicio', '<=', $hoy)
            ->whereDate('fecha_fin', '>=', $hoy)
            ->first();
        if (!$jornadaActiva) {
            $jornadaActiva = $liguilla->torneo->jornadas()
                ->whereDate('fecha_inicio', '>=', $hoy)
                ->orderBy('fecha_inicio', 'asc')
                ->first();
        }
        // 4️⃣ Alineación del usuario en la jornada actual
        $alineacion = null;
        if ($jornadaActiva) {
            $alineacion = Alineacion::with('jugadores')
                ->where('jornada_id', $jornadaActiva->id)
                ->where('cuenta_id', $usuarioId)
                ->first();
        }

        // 5️⃣ Resultados de partidos de la última jornada
        $resultados = $jornadaActiva
            ? $jornadaActiva->partidos()->with(['equipoLocal', 'equipoVisitante'])->get()
            : collect();

        // Plantilla de usuario
        $plantilla = Plantilla::with('jugadores')
            ->where('liguilla_id', $liguilla->id)
            ->where('cuenta_id', $usuarioId)
            ->first();
        $miPlantilla = $plantilla?->jugadores ?? collect();

        // 7️⃣ Comprobar si la jornada ya ha empezado
        $bloqueada = false;
        if ($jornadaActiva) {
            $primerPartido = $jornadaActiva->partidos()->orderBy('fecha_partido', 'asc')->first();
            if ($primerPartido && now()->gte($primerPartido->fecha_partido)) {
                $bloqueada = true;
            }
        }
        return view('user.liguilla', compact(
            'liguilla',
            'clasificacion',
            'alineacion',
            'resultados',
            'jornadaActiva',
            'usuario',
            'miPlantilla',
            'bloqueada'
        ));
    }
}
