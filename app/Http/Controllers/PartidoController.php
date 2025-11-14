<?php

namespace App\Http\Controllers;

use App\Models\Estadistica;
use App\Models\Jornada;
use App\Models\Partido;
use App\Models\Torneo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PartidoController extends Controller
{
    public function mostrarPaginaJornadas($idTorneo)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $torneo = Torneo::with(['jornadas' => function ($query) {
            $query->orderBy('orden');
        }])->findOrFail($idTorneo);

        return view('admin.jornadas', compact('torneo'));
    }
    public function crearJornada(Request $request, $idTorneo)
    {
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        // Validar los datos del formulario
        $validated = $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            ],
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'nombre.string' => 'El nombre debe ser un string',
                'nombre.max' => 'El nombre debe tener un maximo de 255 caracteres',
                'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha valida.',
                'fecha_fin.date' => 'La fecha de fin debe ser una fecha valida.',
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            ]
        );
        $torneo = Torneo::findOrFail($idTorneo);
        
        $fecha_inicio = Carbon::parse($validated['fecha_inicio']);
        $fecha_fin = Carbon::parse($validated['fecha_fin']);

        // Validar que las fechas de la jornada estén dentro del rango del torneo
        if (($validated['fecha_inicio'] && $fecha_inicio->gt($torneo->fecha_fin)) ||
            ($validated['fecha_fin'] && $fecha_fin->lt($torneo->fecha_inicio))
        ) {
            return redirect('/admin/torneos/' . $idTorneo . '/jornadas')
                ->withErrors(['fecha_jornada' => 'Las fechas de inicio y fin de la jornada deben estar dentro del rango del torneo.'])
                ->withInput();
        }

        // Obtener el siguiente orden
        $maxOrden = Jornada::where('torneo_id', $idTorneo)->max('orden');
        $nuevoOrden = $maxOrden ? $maxOrden + 1 : 1;

        $jornada = new Jornada();
        $jornada->torneo_id = $idTorneo;
        $jornada->nombre = $validated['nombre'];
        $jornada->fecha_inicio = $validated['fecha_inicio'];
        $jornada->fecha_fin = $validated['fecha_fin'];
        $jornada->orden = $nuevoOrden;
        $jornada->save();
        // Redirigir a la página de torneos con un mensaje de éxito
        return redirect('/admin/torneos/' . $idTorneo . '/jornadas')->with('success', 'Jornada creada correctamente.');
    }
    public function editarJornada(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        // Validar los datos del formulario
        $validated = $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            ],
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'nombre.string' => 'El nombre debe ser un string',
                'nombre.max' => 'El nombre debe tener un máximo de 255 caracteres',
                'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
                'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            ]
        );
        $jornada = Jornada::findOrFail($id);
        $torneo = $jornada->torneo;
        if ($torneo) {
            $fecha_inicio = Carbon::parse($validated['fecha_inicio']);
            $fecha_fin = Carbon::parse($validated['fecha_fin']);

            // Validar que las fechas de la jornada estén dentro del rango del torneo
            if (($validated['fecha_inicio'] && $fecha_inicio->gt($torneo->fecha_fin)) ||
                ($validated['fecha_fin'] && $fecha_fin->lt($torneo->fecha_inicio))
            ) {
                return redirect('/admin/torneos/' . $torneo->id . '/jornadas')
                    ->withErrors(['fecha_jornada' => 'Las fechas de inicio y fin de la jornada deben estar dentro del rango del torneo.'])
                    ->withInput();
            }
            $jornada->nombre = $validated['nombre'];
            $jornada->fecha_inicio = $validated['fecha_inicio'];
            $jornada->fecha_fin = $validated['fecha_fin'];
            $jornada->save();
            return redirect('/admin/torneos/' . $jornada->torneo_id . '/jornadas')->with('success', 'Jornada actualizada correctamente.');
        } else {
            return redirect('/admin/torneos')
                ->withErrors(['torneo' => 'Torneo no encontrado.'])
                ->withInput();
        }
    }

    public function guardarOrdenJornadas(Request $request, $idTorneo)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $ordenData = json_decode($request->input('orden'), true);

        foreach ($ordenData as $item) {
            Jornada::where('id', $item['id'])->update(['orden' => $item['orden']]);
        }

        return redirect()->back()->with('success', 'Orden de jornadas actualizado correctamente.');
    }
    public function eliminarJornada($id)
    {
        // Verificar si el usuario es administrador
       if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $jornada = Jornada::findOrFail($id);
        
        $ordenEliminado = $jornada->orden;
        // Eliminar los partidos de la base de datos
        $jornada->partidos()->delete();
        $jornada->delete();
        //Cambiar el orden de las jornadas restantes
        Jornada::where('orden', '>', $ordenEliminado)
            ->decrement('orden');
        return redirect('/admin/torneos/' . $jornada->torneo_id . '/jornadas')->with('success', 'Jornada eliminada correctamente.');
    }

    public function mostrarPaginaPartido($id)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        // Lógica para mostrar la página de un partido específico
        $partido = Partido::with(['equipoLocal', 'equipoVisitante', 'jornada.torneo'])
            ->findOrFail($id);
        //Equipos del torneo del partido
        $equipos = $partido->jornada->torneo->equipos;
        // Jugadores del equipo local inscritos en el torneo, añadiendo el id del equipo con el que están inscritos
        $jugadoresLocal = $partido->equipoLocal->jugadoresEnTorneo($partido->jornada->torneo->id)
            ->each(function ($jugador) use ($partido) {
                $jugador->equipo_id = $partido->equipoLocal->id;
            });

        // Jugadores del equipo visitante inscritos en el torneo, añadiendo el id del equipo con el que están inscritos
        $jugadoresVisitante = $partido->equipoVisitante->jugadoresEnTorneo($partido->jornada->torneo->id)
            ->each(function ($jugador) use ($partido) {
                $jugador->equipo_id = $partido->equipoVisitante->id;
            });

        // Todos los jugadores del partido
        $jugadores = $jugadoresLocal->merge($jugadoresVisitante);
        return view('admin.partido', compact('partido', 'equipos', 'jugadoresLocal', 'jugadoresVisitante', 'jugadores'));
    }

    public function crearPartido(Request $request, $idJornada)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $jornada = Jornada::findOrFail($idJornada);
       
        // Validar los datos del formulario
        $validated=$request->validate(
            [
                'equipo_local_id' => 'required|exists:equipos,id',
                'equipo_visitante_id' => 'required|exists:equipos,id|different:equipo_local_id',
                'fecha_partido' => 'required|date',
                'goles_local' => 'nullable|integer|min:0',
                'goles_visitante' => 'nullable|integer|min:0',
                'estado' => 'nullable|in:programado,jugado,cancelado',
                'eventos' => 'nullable|array',
            ],
            [
                'equipo_local_id.required' => 'El equipo local es obligatorio.',
                'equipo_local_id.exists' => 'El equipo local debe existir.',
                'equipo_visitante_id.required' => 'El equipo visitante es obligatorio.',
                'equipo_visitante_id.exists' => 'El equipo visitante debe existir.',
                'equipo_visitante_id.different' => 'El equipo visitante debe ser diferente al equipo local.',
                'fecha_partido.required' => 'La fecha del partido es obligatoria.',
                'goles_local.integer' => 'Los goles del equipo local deben ser un número entero.',
                'goles_visitante.integer' => 'Los goles del equipo visitante deben ser un número entero.',
                'estado.required' => 'El estado del partido es obligatorio.',
                'estado.in' => 'El estado del partido debe ser uno de los siguientes: programado, jugado, cancelado.',
                'eventos.array' => 'Los eventos deben ser un array.',
            ]
        );
        $torneo = Torneo::findOrFail($jornada->torneo->id);
        
        //Comprobar que la fecha sea posterior a fecha_inicio y anterior a fecha_fin del torneo
        $fecha_partido = Carbon::parse($validated['fecha_partido']);
        if ($fecha_partido->lt($torneo->fecha_inicio) || $fecha_partido->gt($torneo->fecha_fin)) {
            return redirect('/admin/torneos/' . $torneo->id . '/jornadas')
                ->withErrors(['fecha_partido' => 'La fecha del partido debe estar dentro del rango del torneo.'])
                ->withInput();
        }
        $hora_partido = $validated['hora_partido'] ?? '00:00:00';
        // Combinar fecha y hora en un solo campo
        $fecha_hora_partido = $fecha_partido->format('Y-m-d') . ' ' . $hora_partido;
        // Crear un nuevo partido
        $partido = new Partido();
        $partido->jornada_id = $idJornada;
        $partido->equipo_local_id = $validated['equipo_local_id'];
        $partido->equipo_visitante_id = $validated['equipo_visitante_id'];
        $partido->fecha_partido = $fecha_hora_partido;
        $partido->goles_local = $validated['goles_local'];
        $partido->goles_visitante = $validated['goles_visitante'];
        $partido->estado = $validated['estado'] ?? 'programado';
        $partido->eventos = $validated['eventos'] ?? null;
        $partido->save();
        // Redirigir a la página de torneos con un mensaje de éxito
        return redirect('/admin/torneos/' . $torneo->id . '/jornadas')->with('success', 'Partido creado correctamente.');
    }

    public function eliminarPartido($id)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
       
        $partido = Partido::findOrFail($id);
        $id_torneo = $partido->jornada->torneo->id;
        // Eliminar el partido de la base de datos
        $partido->delete();
        return redirect('/admin/torneos/' . $id_torneo . '/jornadas')->with('success', 'Partido eliminado correctamente.');
    }

    public function editarPartido(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        // Validar los datos del formulario
        $validated = $request->validate(
            [
                'equipo_local_id' => 'required|exists:equipos,id',
                'equipo_visitante_id' => 'required|exists:equipos,id|different:equipo_local_id',
                'goles_local' => 'nullable|integer|min:0',
                'goles_visitante' => 'nullable|integer|min:0',
                'fecha_partido' => 'required|date',
                'hora_partido' => 'nullable|date_format:H:i',
                'estado' => 'nullable|in:programado,jugado,cancelado',
            ],
            [
                'equipo_local_id.required' => 'El equipo local es obligatorio.',
                'equipo_local_id.exists' => 'El equipo local debe existir.',
                'equipo_visitante_id.required' => 'El equipo visitante es obligatorio.',
                'equipo_visitante_id.exists' => 'El equipo visitante debe existir.',
                'equipo_visitante_id.different' => 'El equipo visitante debe ser diferente al equipo local.',
                'goles_local.integer' => 'Los goles del equipo local deben ser un número entero.',
                'goles_visitante.integer' => 'Los goles del equipo visitante deben ser un número entero.',
                'estado.required' => 'El estado del partido es obligatorio.',
                'estado.in' => 'El estado del partido debe ser uno de los siguientes: programado, jugado, cancelado.',
                'fecha_partido.required' => 'La fecha del partido es obligatoria.',
                'hora_partido.date_format' => 'La hora del partido debe tener el formato HH:MM.',
            ]
        );
        //Buscar el partido por ID
        $partido = Partido::findOrFail($id);
        $torneo = $partido->jornada->torneo;
        if (!$torneo) {
            return redirect('/admin/torneos')
                ->withErrors(['torneo' => 'Torneo no encontrado.'])
                ->withInput();
        }
        //Comprobar que la fecha sea posterior a fecha_inicio y anterior a fecha_fin del torneo
        $fecha_partido = Carbon::parse($validated['fecha_partido']);
        if ($fecha_partido->lt($torneo->fecha_inicio) || $fecha_partido->gt($torneo->fecha_fin)) {
            return redirect('/admin/partidos/' . $id)
                ->withErrors(['fecha_partido' => 'La fecha del partido debe estar dentro del rango del torneo.'])
                ->withInput();
        }
        $hora_partido = $validated['hora_partido'] ?? '00:00:00';
        // Combinar fecha y hora en un solo campo
        $fecha_hora_partido = $fecha_partido->format('Y-m-d') . ' ' . $hora_partido;
        // Actualizar los datos del partido
        $partido->equipo_local_id = $validated['equipo_local_id'];
        $partido->equipo_visitante_id = $validated['equipo_visitante_id'];
        $partido->goles_local = $validated['goles_local'];
        $partido->goles_visitante = $validated['goles_visitante'];
        $partido->fecha_partido = $fecha_hora_partido;
        $partido->estado = $validated['estado'];
        $partido->save();

        // Recalcula desde cero
        $partido->actualizarEstadisticas();

        return redirect("/admin/partidos/{$id}")->with('success', 'Partido actualizado correctamente.');
    }
    public function actualizarResultado(Request $request)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $partido = Partido::findOrFail($request->partido_id);
        $partido->goles_local = $request->goles_local;
        $partido->goles_visitante = $request->goles_visitante;
        if (!is_null($request->goles_local) && !is_null($request->goles_visitante)) {
            $partido->estado = 'jugado';
        }
        $partido->save();
        // 👉 Limpia estadísticas previas de este partido
        Estadistica::where('partido_id', $partido->id)->delete();

        // 👉 Determina resultado base y puntos por equipo
        if ($partido->estado === 'jugado') {
            if ($partido->goles_local > $partido->goles_visitante) {
                $resultadoLocal = 'ganado';
                $resultadoVisitante = 'perdido';
                $puntosLocal = 3;
                $puntosVisitante = 0;
            } elseif ($partido->goles_local < $partido->goles_visitante) {
                $resultadoLocal = 'perdido';
                $resultadoVisitante = 'ganado';
                $puntosLocal = 0;
                $puntosVisitante = 3;
            } else {
                $resultadoLocal = $resultadoVisitante = 'empatado';
                $puntosLocal = $puntosVisitante = 1;
            }

            // Crea stats para Local
            foreach ($partido->equipoLocal->jugadoresEnTorneo($partido->jornada->torneo->id) as $jugador) {
                Estadistica::create([
                    'jugador_id' => $jugador->id,
                    'partido_id' => $partido->id,
                    'resultado' => $resultadoLocal,
                    'puntos' => $puntosLocal,
                ]);
            }

            //  Crea stats para Visitante
            foreach ($partido->equipoVisitante->jugadoresEnTorneo($partido->jornada->torneo->id) as $jugador) {
                Estadistica::create([
                    'jugador_id' => $jugador->id,
                    'partido_id' => $partido->id,
                    'resultado' => $resultadoVisitante,
                    'puntos' => $puntosVisitante,
                ]);
            }
        }

        // Sumar goles, asistencias y tarjetas a cada jugador a partir de los eventos JSON
        $eventos = json_decode($partido->eventos, true);

        if ($eventos && is_array($eventos)) {
            foreach ($eventos as $evento) {
                if (empty($evento['jugador_id'])) continue;

                $stat = Estadistica::firstOrCreate([
                    'jugador_id' => $evento['jugador_id'],
                    'partido_id' => $partido->id,
                ]);

                switch ($evento['tipo']) {
                    case 'Gol':
                        $stat->goles += 1;
                        $stat->puntos += 5; // Por gol
                        break;

                    case 'Asistencia':
                        $stat->asistencias += 1;
                        $stat->puntos += 3; // Por asistencia
                        break;

                    case 'Tarjeta Amarilla':
                        $stat->tarjetas_amarillas += 1;
                        $stat->puntos -= 3;
                        break;

                    case 'Tarjeta Roja':
                        $stat->tarjetas_rojas += 1;
                        $stat->puntos -= 5;
                        break;

                    case 'Falta':
                        $stat->faltas += 1;
                        $stat->puntos -= 1;
                        break;

                    case 'Parada':
                        $stat->paradas += 1;
                        $stat->puntos += 2;
                        break;
                }

                $stat->save();
            }
        }

        return redirect()->back()->with('success', 'Resultado y eventos guardados correctamente.');
    }

    public function agregarEvento(Request $request, $id)
    {
        $partido = Partido::findOrFail($id);

        $request->validate([],);
        // Validar los datos del formulario
        $validator = Validator::make(
            $request->all(),
            [
                'eventos' => 'nullable|array',
            ],
        );
        if ($validator->fails()) {
            return response()->json(['status' => 'error']);
        }

        $partido->eventos = json_encode($request->eventos);
        $partido->save();

        // Limpiar estadísticas de ese partido
        foreach ($partido->estadisticas as $stat) {
            $stat->goles = 0;
            $stat->asistencias = 0;
            $stat->tarjetas_amarillas = 0;
            $stat->tarjetas_rojas = 0;
            $stat->faltas = 0;
            $stat->puntos = 0;

            $equipoJugador = $stat->jugador->participaciones->firstWhere('id', $partido->jornada->torneo->id);
            $equipo_id = $equipoJugador ? $equipoJugador->pivot->equipo_id : null;
            // Puntos base por resultado
            if ($partido->goles_local > $partido->goles_visitante) {
                if ($equipo_id == $partido->equipo_local_id) {
                    $stat->resultado = 'ganado';
                    $stat->puntos = 3;
                } elseif ($equipo_id == $partido->equipo_visitante_id) {
                    $stat->resultado = 'perdido';
                    $stat->puntos = 0;
                }
            } elseif ($partido->goles_local < $partido->goles_visitante) {
                if ($equipo_id == $partido->equipo_visitante_id) {
                    $stat->resultado = 'ganado';
                    $stat->puntos = 3;
                } elseif ($equipo_id == $partido->equipo_local_id) {
                    $stat->resultado = 'perdido';
                    $stat->puntos = 0;
                }
            } else {
                $stat->resultado = 'empatado';
                $stat->puntos = 1;
            }
            $stat->save();
        }
        // Guardar estadísticas de los jugadores involucrados en los eventos
        foreach ($request->eventos as $evento) {
            $stat = Estadistica::firstOrCreate([
                'jugador_id' => $evento['jugador_id'],
                'partido_id' => $partido->id,
            ]);
            switch ($evento['tipo']) {
                case 'Gol':
                    $stat->goles += 1;
                    $stat->puntos += 5;
                    break;

                case 'Asistencia':
                    $stat->asistencias += 1;
                    $stat->puntos += 3;
                    break;

                case 'Tarjeta Amarilla':
                    $stat->tarjetas_amarillas += 1;
                    $stat->puntos -= 3;
                    break;

                case 'Tarjeta Roja':
                    $stat->tarjetas_rojas += 1;
                    $stat->puntos -= 5;
                    break;

                case 'Falta':
                    $stat->faltas += 1;
                    $stat->puntos -= 1;
                    break;

                case 'Parada':
                    $stat->paradas += 1;
                    $stat->puntos += 2;
                    break;
            }
            $stat->save();
        }
        return response()->json(['status' => 'ok']);
    }
}
