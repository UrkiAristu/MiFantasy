<?php

namespace App\Http\Controllers;

use App\Models\Alineacion;
use App\Models\Cuenta;
use App\Models\Estadistica;
use App\Models\Jugador;
use App\Models\Liguilla;
use App\Models\Plantilla;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LiguillaController extends Controller
{
    public function mostrarPaginaLiguillas()
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $liguillas = Liguilla::all();

        // Retornar la vista con los datos de los equipos
        return view('admin.liguillas', compact('liguillas'));
    }
    public function crearLiguilla(Request $request)
    {
        // Validar los datos del formulario
        $validated = $request->validate(
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

        $usuario_id = Auth::id();
        $torneo = Torneo::findOrFail($validated['torneo_id']);
        $liguilla = new Liguilla();
        $liguilla->nombre = $validated['nombre'];
        $liguilla->torneo_id = $torneo->id;
        $liguilla->max_usuarios = $validated['num_max_part'];
        $liguilla->creador_id = $usuario_id;
        $liguilla->codigo_unico = Str::random(8); // Código para unirse
        $liguilla->save();

        // Añadir al creador como primer usuario
        $liguilla->usuarios()->attach($usuario_id);
        // Crear plantilla aleatoria para este usuario en la liguilla
        $this->crearPlantillaAleatoria($liguilla->id, $usuario_id);
        // Redirigir a la página de torneos con un mensaje de éxito
        return redirect('/user/liguillas')->with('success', 'Ligulla creada correctamente.');
    }
    public function mostrarPaginaLiguillasUser()
    {
        /** @var \App\Models\User $usuario */
        $usuario = Auth::user();
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
        $validated = $request->validate([
            'codigo' => 'required|string|size:8', // suponiendo código de 8 caracteres
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.size' => 'El código debe tener exactamente 8 caracteres.',
        ]);

        $codigo = strtoupper($validated['codigo']); // uniformizar mayúsculas

        // Buscar liguilla por código único
        $liguilla = Liguilla::where('codigo_unico', $codigo)->first();

        if (!$liguilla) {
            return redirect()->back()->withErrors(['codigo' => 'Código de liguilla no válido.'])->withInput();
        }

        $usuarioId = Auth::id();

        // Comprobar si el usuario ya está en esa liguilla
        if ($liguilla->usuarios()->where('user_id', $usuarioId)->exists()) {
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
            'user_id' => $usuarioId
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
        $usuario = Auth::user();

        // 1️⃣ Liguilla y torneo
        $liguilla = Liguilla::with(['torneo.jornadas.partidos','plantillas.jugadores','plantillas.usuario'])
            ->findOrFail($id);

        // 2️⃣ Clasificación general
        $clasificacion = $liguilla->usuarios()
            ->withPivot('puntos')
            ->orderByDesc('pivot_puntos')
            ->get()
            ->map(function ($usuario, $index) {
                return (object) [
                    'id' => $usuario->id,
                    'posicion' => $index + 1,
                    'name' => $usuario->name,
                    'email' => $usuario->email,
                    'puntos' => $usuario->pivot->puntos ?? 0
                ];
            });

        // 3️⃣ Jornada actual o próxima + jornadas con partidos
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

        $jornadas = $liguilla->torneo->jornadas()
        ->with(['partidos.equipoLocal', 'partidos.equipoVisitante'])
        ->orderBy('orden')
        ->get();

        // 4️⃣ Alineación BASE del usuario + alineaciones congeladas
        $alineacionBase = Alineacion::with('jugadores')
        ->where('liguilla_id', $liguilla->id)
        ->where('user_id', $usuario->id)
        ->whereNull('jornada_id')
        ->first();
        $jugadoresBase = $alineacionBase?->jugadores ?? collect();

        $misAlineaciones = Alineacion::with(['jornada', 'jugadores'])
        ->where('liguilla_id', $liguilla->id)
        ->where('user_id', $usuario->id)
        ->whereNotNull('jornada_id') // solo las "fotos" de jornada
        ->get();

        // 5️⃣ Resultados de partidos de la última jornada
        $resultados = $jornadaActiva
            ? $jornadaActiva->partidos()->with(['equipoLocal', 'equipoVisitante'])->get()
            : collect();

        // Plantilla de usuario
        $plantilla = Plantilla::with('jugadores')
            ->where('liguilla_id', $liguilla->id)
            ->where('user_id', $usuario->id)
            ->first();
        $miPlantilla = $plantilla?->jugadores ?? collect();

        // 7️⃣ Comprobar si la jornada ya ha empezado
        $bloqueada = false;

        return view('user.liguilla', compact(
            'liguilla',
            'clasificacion',
            'jornadaActiva',
            'jornadas',
            'usuario',
            'miPlantilla',
            'jugadoresBase',
            'misAlineaciones',
            'resultados',
            'bloqueada'
        ));
    }
    public function plantilla($idLiguilla, $idUser)
    {
        $liguilla = Liguilla::findOrFail($idLiguilla);
        $user = User::findOrFail($idUser);
        // Plantilla de ese usuario en esa liguilla
        $plantilla = $liguilla->plantillas()
            ->with(['jugadores'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        return view('user.plantilla-participante',compact('liguilla', 'user', 'plantilla'));
    }

    public function clasificacionAjax(Liguilla $liguilla, Request $request)
    {
        $modoClasificacion = $request->get('modo_clasificacion', 'global');
        $jornadaSeleccionada = null;

        if ($modoClasificacion === 'global') {
            // Clasificación TOTAL usando el pivot 'puntos' de liguilla_usuario
            $clasificacion = $liguilla->usuarios()
                ->withPivot('puntos')
                ->orderByDesc('pivot_puntos')
                ->get()
                ->map(function ($usuario, $index) {
                    return [
                        'id'       => $usuario->id,
                        'posicion' => $index + 1,
                        'name'     => $usuario->name,
                        'email'    => $usuario->email,
                        'puntos'   => $usuario->pivot->puntos ?? 0,
                    ];
                })
                ->values();
        } else {
            $jornadaSeleccionada = $liguilla->torneo->jornadas()->find($modoClasificacion);

            if ($jornadaSeleccionada) {
                $puntosPorUsuario = DB::table('alineaciones as a')
                    ->join('alineacion_jugador as aj', 'aj.alineacion_id', '=', 'a.id')
                    ->select('a.user_id', DB::raw('SUM(aj.puntos) as total_puntos'))
                    ->where('a.liguilla_id', $liguilla->id)
                    ->where('a.jornada_id', $jornadaSeleccionada->id)
                    ->groupBy('a.user_id')
                    ->pluck('total_puntos', 'user_id');

                $usuarios = $liguilla->usuarios()
                    ->whereIn('users.id', $puntosPorUsuario->keys())
                    ->get();

                $clasificacion = $usuarios
                    ->sortByDesc(function ($u) use ($puntosPorUsuario) {
                        return $puntosPorUsuario[$u->id] ?? 0;
                    })
                    ->values()
                    ->map(function ($usuario, $index) use ($puntosPorUsuario) {
                        return [
                            'id'       => $usuario->id,
                            'posicion' => $index + 1,
                            'name'     => $usuario->name,
                            'email'    => $usuario->email,
                            'puntos'   => $puntosPorUsuario[$usuario->id] ?? 0,
                        ];
                    })
                    ->values();
            } else {
                $clasificacion = collect();
            }
        }

        return response()->json([
            'modo'         => $modoClasificacion,
            'jornada'      => $jornadaSeleccionada ? [
                'id'     => $jornadaSeleccionada->id,
                'nombre' => $jornadaSeleccionada->nombre,
                'orden'  => $jornadaSeleccionada->orden,
            ] : null,
            'clasificacion' => $clasificacion,
        ]);
    }

    public function alineacionUsuarioJornada(Liguilla $liguilla, User $user, $jornadaId)
    {
        $alineacion = Alineacion::with('jugadores')
            ->where('liguilla_id', $liguilla->id)
            ->where('user_id', $user->id)
            ->where('jornada_id', $jornadaId)
            ->first();

        if (!$alineacion) {
            return response()->json([
                'status'       => 'ok',
                'jugadores'    => [],
                'total_puntos' => 0,
            ]);
        }

        $jugadores = $alineacion->jugadores->map(function ($jug) use ($jornadaId) {
            // suma de puntos de este jugador en los partidos de esa jornada
            $puntos = Estadistica::where('jugador_id', $jug->id)
                ->whereHas('partido', function ($q) use ($jornadaId) {
                    $q->where('jornada_id', $jornadaId);
                })
                ->sum('puntos');

            return [
                'id'        => $jug->id,
                'nombre'    => $jug->nombre,
                'apellido1' => $jug->apellido1,
                'foto'      => $jug->foto
                    ? asset('storage/' . $jug->foto)
                    : asset('assets/media/images/default-player.png'),
                'puntos'    => $puntos,
            ];
        });

        $totalPuntos = $jugadores->sum('puntos');

        return response()->json([
            'status'       => 'ok',
            'jugadores'    => $jugadores,
            'total_puntos' => $totalPuntos,
        ]);
    }

}
