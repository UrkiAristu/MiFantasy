<?php

namespace App\Http\Controllers;

use App\Actions\Plantilla\GenerarPlantillaAleatoriaAction;
use App\Models\Alineacion;
use App\Models\Liguilla;
use App\Models\Plantilla;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LiguillaController extends Controller
{
    public function mostrarPaginaLiguillas()
    {
        $liguillas = Liguilla::all();

        // Retornar la vista con los datos de los equipos
        return view('admin.liguillas', compact('liguillas'));
    }

    public function crearLiguilla(Request $request, GenerarPlantillaAleatoriaAction $generarPlantilla)
    {
        // Validar los datos del formulario
        $validated = $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'num_max_part' => 'required|integer|min:2|max:100',
                'torneo_id' => 'required|integer|exists:torneos,id',
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
        $liguilla = new Liguilla;
        $liguilla->nombre = $validated['nombre'];
        $liguilla->torneo_id = $torneo->id;
        $liguilla->max_usuarios = $validated['num_max_part'];
        $liguilla->creador_id = $usuario_id;
        $liguilla->codigo_unico = Str::random(8); // Código para unirse
        $liguilla->save();

        // Añadir al creador como primer usuario
        $liguilla->usuarios()->attach($usuario_id);
        // Crear plantilla aleatoria para este usuario en la liguilla
        $generarPlantilla->execute($liguilla->id, (int) $usuario_id);

        // Redirigir a la página de torneos con un mensaje de éxito
        return redirect('/user/liguillas')->with('success', 'Ligulla creada correctamente.');
    }

    public function mostrarPaginaLiguillasUser()
    {
        /** @var User|null $usuario */
        $usuario = Auth::user();
        if (! $usuario) {
            return redirect('/login')->withErrors('Usuario no encontrado.');
        }

        // Obtener las liguillas con el torneo, datos del pivot y ranking de participantes
        $liguillasUsuario = $usuario->liguillas()
            ->with(['torneo', 'usuarios' => function ($q) {
                $q->withPivot('puntos')->orderByDesc('pivot_puntos');
            }])
            ->get()
            ->map(function ($liguilla) use ($usuario) {
                $usuariosOrdenados = $liguilla->usuarios->sortByDesc(fn ($u) => $u->pivot->puntos ?? 0)->values();
                $posicion = $usuariosOrdenados->search(fn ($u) => $u->id === $usuario->id);

                $liguilla->posicion_usuario = $posicion !== false ? ($posicion + 1) : ($liguilla->pivot->puesto ?? 'N/D');
                $liguilla->puntos_usuario = $liguilla->pivot->puntos ?? 0;

                return $liguilla;
            });

        return view('user.liguillas', compact('liguillasUsuario'));
    }

    public function mostrarPaginaUnirseLiguillasUser(Request $request)
    {
        $codigo = $request->query('codigo'); // o $request->input('codigo')

        return view('user.unirseLiguilla', compact('codigo'));
    }

    public function unirseLiguilla(Request $request, GenerarPlantillaAleatoriaAction $generarPlantilla)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|size:8', // suponiendo código de 8 caracteres
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.size' => 'El código debe tener exactamente 8 caracteres.',
        ]);

        $codigo = strtoupper($validated['codigo']); // uniformizar mayúsculas
        $usuarioId = Auth::id();

        $resultado = DB::transaction(function () use ($codigo, $usuarioId, $generarPlantilla) {
            // Cargar liguilla con bloqueo pesimista para prevenir condiciones de carrera en cupo
            $liguilla = Liguilla::where('codigo_unico', $codigo)->lockForUpdate()->first();

            if (! $liguilla) {
                return ['status' => 'error', 'message' => 'Código de liguilla no válido.'];
            }

            // Comprobar si el usuario ya está en esa liguilla
            if ($liguilla->usuarios()->where('user_id', $usuarioId)->exists()) {
                return ['status' => 'error', 'message' => 'Ya estás inscrito en esta liguilla.'];
            }

            // Comprobar si la liguilla está llena
            if ($liguilla->usuarios()->count() >= $liguilla->max_usuarios) {
                return ['status' => 'error', 'message' => 'La liguilla ya está completa.'];
            }

            // Añadir usuario a la liguilla
            $liguilla->usuarios()->attach($usuarioId);

            // Crear plantilla aleatoria para este usuario en la liguilla
            $generarPlantilla->execute($liguilla->id, (int) $usuarioId);

            return ['status' => 'success'];
        });

        if ($resultado['status'] === 'error') {
            return redirect()->back()->withErrors(['codigo' => $resultado['message']])->withInput();
        }

        return redirect('/user/liguillas')->with('success', 'Te has unido correctamente a la liguilla.');
    }

    public function mostrarPaginaLiguillaUser($id)
    {
        $usuario = Auth::user();

        // 1️⃣ Liguilla y torneo
        $liguilla = Liguilla::query()
            ->select(['id', 'nombre', 'torneo_id', 'codigo_unico', 'max_usuarios', 'creador_id'])
            ->with([
                'torneo' => function ($q) {
                    $q->select(['id', 'nombre', 'logo', 'modalidad']);
                },
                'plantillas' => function ($q) {
                    $q->select(['id', 'liguilla_id', 'user_id'])
                        ->with([
                            'usuario' => function ($u) {
                                $u->select(['id', 'name', 'email']);
                            },
                            'jugadores' => function ($j) {
                                $j->select(['jugadores.id']);
                            },
                        ]);
                },
            ])
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
                    'puntos' => $usuario->pivot->puntos ?? 0,
                ];
            });

        // 3️⃣ Jornada actual o próxima + jornadas con partidos
        $hoy = now();
        $jornadaActiva = $liguilla->torneo->jornadas()
            ->whereDate('fecha_inicio', '<=', $hoy)
            ->whereDate('fecha_fin', '>=', $hoy)
            ->first();
        if (! $jornadaActiva) {
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
        $alineacionBase = Alineacion::with('jugadores.equipos')
            ->where('liguilla_id', $liguilla->id)
            ->where('user_id', $usuario->id)
            ->whereNull('jornada_id')
            ->first();
        $jugadoresBase = $alineacionBase ? $alineacionBase->jugadores : collect();

        $misAlineaciones = Alineacion::with(['jornada', 'jugadores.equipos'])
            ->where('liguilla_id', $liguilla->id)
            ->where('user_id', $usuario->id)
            ->whereNotNull('jornada_id') // solo las "fotos" de jornada
            ->get();

        // 5️⃣ Resultados de partidos de la última jornada
        $resultados = $jornadaActiva
            ? $jornadaActiva->partidos()->with(['equipoLocal', 'equipoVisitante'])->get()
            : collect();

        // Plantilla de usuario
        $plantilla = Plantilla::with('jugadores.equipos')
            ->where('liguilla_id', $liguilla->id)
            ->where('user_id', $usuario->id)
            ->first();
        $miPlantilla = $plantilla ? $plantilla->jugadores : collect();

        // Hidratar relación equipo y puntos para cada jugador
        foreach ($miPlantilla as $jugador) {
            $jugador->equipo = $jugador->equipos->first() ?? $jugador->equipoEnTorneo($liguilla->torneo_id);
            $stats = $jugador->resumenEstadisticasEnTorneo($liguilla->torneo_id);
            $jugador->puntos_totales = $stats['puntos'];
            $jugador->precio = $jugador->precio ?? 1000000;
        }
        if ($alineacionBase) {
            foreach ($alineacionBase->jugadores as $jugador) {
                $jugador->equipo = $jugador->equipos->first() ?? $jugador->equipoEnTorneo($liguilla->torneo_id);
            }
        }
        foreach ($misAlineaciones as $alineacion) {
            foreach ($alineacion->jugadores as $jugador) {
                $jugador->equipo = $jugador->equipos->first() ?? $jugador->equipoEnTorneo($liguilla->torneo_id);
            }
        }

        // 6️⃣ Formaciones disponibles según la modalidad del torneo
        $modalidad = (string) ($liguilla->torneo->modalidad ?? 'sala');
        $formacionesDisponibles = AlineacionController::obtenerFormacionesPorModalidad($modalidad);
        $formaciones = array_combine(array_keys($formacionesDisponibles), array_keys($formacionesDisponibles));
        $formacionActiva = $alineacionBase->formacion
            ?? $liguilla->torneo->formacion_por_defecto
            ?? array_key_first($formacionesDisponibles);
        $formacionActual = $formacionActiva;

        // Variables de compatibilidad con la vista
        $plantillaUsuario = $miPlantilla;
        $alineacionActual = $alineacionBase;
        $proximaJornada = $jornadaActiva;
        $jornadasDisponibles = $jornadas;
        $partidosTorneo = $resultados;

        // 7️⃣ Comprobar si la jornada ya ha empezado
        $bloqueada = false;

        $jornadas = \App\Models\Jornada::where('torneo_id', $liguilla->torneo_id)->orderBy('orden')->get();
        $jornadaSeleccionada = request()->filled('jornada_id') ? $jornadas->firstWhere('id', request('jornada_id')) : ($jornadas->where('fecha_fin', '<=', now()->toDateString())->last() ?? $jornadas->first());
        $partidos = $jornadaSeleccionada ? \App\Models\Partido::where('jornada_id', $jornadaSeleccionada->id)->with(['equipoLocal', 'equipoVisitante'])->get() : collect();

        return view('user.liguilla', compact(
            'liguilla',
            'clasificacion',
            'jornadaActiva',
            'jornadas',
            'jornadaSeleccionada',
            'partidos',
            'usuario',
            'miPlantilla',
            'plantillaUsuario',
            'jugadoresBase',
            'alineacionBase',
            'alineacionActual',
            'misAlineaciones',
            'resultados',
            'partidosTorneo',
            'proximaJornada',
            'jornadasDisponibles',
            'bloqueada',
            'formaciones',
            'formacionActual',
            'formacionesDisponibles',
            'formacionActiva'
        ));
    }

    public function plantilla($idLiguilla, $idUser)
    {
        $liguilla = Liguilla::findOrFail($idLiguilla);
        $user = User::findOrFail($idUser);

        if (! $liguilla->usuarios()->where('users.id', Auth::id())->exists() ||
            ! $liguilla->usuarios()->where('users.id', $user->id)->exists()) {
            abort(403, 'No tienes permiso para ver esta plantilla');
        }

        // Plantilla de ese usuario en esa liguilla
        $plantilla = $liguilla->plantillas()
            ->with(['jugadores.participaciones'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        return view('user.plantilla-participante', compact('liguilla', 'user', 'plantilla'));
    }

    public function plantillaParticipante($liguilla, $participante)
    {
        abort(404);
    }

    public function clasificacionAjax(Liguilla $liguilla, Request $request)
    {
        if (! $liguilla->usuarios()->where('users.id', Auth::id())->exists()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $modoClasificacion = $request->get('modo_clasificacion', 'global');

        $cacheKey = sprintf(
            'liguilla:%d:clasificacion:modo:%s',
            $liguilla->id,
            $modoClasificacion
        );

        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($liguilla, $modoClasificacion) {
            $jornadaSeleccionada = null;

            if ($modoClasificacion === 'global') {
                $clasificacion = $liguilla->usuarios()
                    ->withPivot('puntos')
                    ->orderByDesc('pivot_puntos')
                    ->get()
                    ->map(function ($usuario, $index) {
                        return [
                            'id' => $usuario->id,
                            'posicion' => $index + 1,
                            'name' => $usuario->name,
                            'email' => $usuario->email,
                            'puntos' => $usuario->pivot->puntos ?? 0,
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
                                'id' => $usuario->id,
                                'posicion' => $index + 1,
                                'name' => $usuario->name,
                                'email' => $usuario->email,
                                'puntos' => $puntosPorUsuario[$usuario->id] ?? 0,
                            ];
                        })
                        ->values();
                } else {
                    $clasificacion = collect();
                }
            }

            return [
                'modo' => $modoClasificacion,
                'jornada' => $jornadaSeleccionada ? [
                    'id' => $jornadaSeleccionada->id,
                    'nombre' => $jornadaSeleccionada->nombre,
                    'orden' => $jornadaSeleccionada->orden,
                ] : null,
                'clasificacion' => $clasificacion,
            ];
        });

        return response()->json($data);
    }

    public function alineacionUsuarioJornada(Liguilla $liguilla, User $user, $jornadaId)
    {
        $authId = Auth::id();
        if (! $liguilla->usuarios()->where('users.id', $authId)->exists() ||
            ! $liguilla->usuarios()->where('users.id', $user->id)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'No autorizado'], 403);
        }

        $alineacion = Alineacion::with('jugadores')
            ->where('liguilla_id', $liguilla->id)
            ->where('user_id', $user->id)
            ->where('jornada_id', $jornadaId)
            ->first();

        if (! $alineacion) {
            return response()->json([
                'status' => 'ok',
                'jugadores' => [],
                'total_puntos' => 0,
            ]);
        }

        $jugadoresIds = $alineacion->jugadores->pluck('id');

        $puntosPorJugador = DB::table('estadisticas as e')
            ->join('partidos as p', 'p.id', '=', 'e.partido_id')
            ->select('e.jugador_id', DB::raw('SUM(e.puntos) as total_puntos'))
            ->where('p.jornada_id', $jornadaId)
            ->whereIn('e.jugador_id', $jugadoresIds)
            ->groupBy('e.jugador_id')
            ->pluck('total_puntos', 'jugador_id');

        $jugadores = $alineacion->jugadores->map(function ($jug) use ($puntosPorJugador) {
            $puntos = $puntosPorJugador[$jug->id] ?? 0;

            return [
                'id' => $jug->id,
                'nombre' => $jug->nombre,
                'apellido1' => $jug->apellido1,
                'foto' => $jug->foto
                    ? asset('storage/'.$jug->foto)
                    : asset('assets/media/images/default-player.png'),
                'puntos' => $puntos,
            ];
        });

        $totalPuntos = $jugadores->sum('puntos');

        return response()->json([
            'status' => 'ok',
            'jugadores' => $jugadores,
            'total_puntos' => $totalPuntos,
        ]);
    }
}
