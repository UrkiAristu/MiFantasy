<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Torneo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PartidoController extends Controller
{
    public function mostrarPaginaPartidos($id)
    {
        $torneo = Torneo::findOrFail($id);
        $partidos = Partido::with(['equipoLocal', 'equipoVisitante', 'torneo'])
            ->where('torneo_id', $id)
            ->orderBy('fecha_partido', 'asc')
            ->get();
        // Lógica para mostrar la página de partidos
        return view('admin.partidos', compact('partidos', 'torneo'));
    }

    public function mostrarPaginaPartido($id)
    {
        // Lógica para mostrar la página de un partido específico
        $partido = Partido::with(['equipoLocal', 'equipoVisitante', 'torneo'])
            ->findOrFail($id);
        //Equipos del torneo del partido
        $equipos = $partido->torneo->equipos;
        // Jugadores del equipo local inscritos en el torneo, añadiendo el id del equipo con el que están inscritos
        $jugadoresLocal = $partido->equipoLocal->jugadoresEnTorneos()
            ->where('torneo_id', $partido->torneo->id)
            ->get()
            ->each(function ($jugador) use ($partido) {
                $jugador->equipo_id = $partido->equipoLocal->id;
            });

        // Jugadores del equipo visitante inscritos en el torneo, añadiendo el id del equipo con el que están inscritos
        $jugadoresVisitante = $partido->equipoVisitante->jugadoresEnTorneos()
            ->where('torneo_id', $partido->torneo->id)
            ->get()
            ->each(function ($jugador) use ($partido) {
                $jugador->equipo_id = $partido->equipoVisitante->id;
            });

        // Todos los jugadores del partido
        $jugadores = $jugadoresLocal->merge($jugadoresVisitante);
        return view('admin.partido', compact('partido', 'equipos', 'jugadoresLocal', 'jugadoresVisitante', 'jugadores'));
    }

    public function crearPartido(Request $request, $id)
    {
        if (session('admin')) {
            // Validar los datos del formulario
            $validator = Validator::make(
                $request->all(),
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
            if ($validator->fails()) {
                return redirect('/admin/torneos/' . $id . '/partidos')
                    ->withErrors($validator)
                    ->withInput();
            }
            $torneo = Torneo::find($id);
            if ($torneo) {
                //Comprobar que la fecha sea posterior a fecha_inicio y anterior a fecha_fin del torneo
                $fecha_partido = Carbon::parse($request->fecha_partido);
                if ($fecha_partido->lt($torneo->fecha_inicio) || $fecha_partido->gt($torneo->fecha_fin)) {
                    dd($fecha_partido, $torneo->fecha_inicio, $torneo->fecha_fin);
                    return redirect('/admin/torneos/' . $id . '/partidos')
                        ->withErrors(['fecha_partido' => 'La fecha del partido debe estar dentro del rango del torneo.'])
                        ->withInput();
                }
                $hora_partido = $request->hora_partido ?? '00:00:00';
                // Combinar fecha y hora en un solo campo
                $fecha_hora_partido = $fecha_partido->format('Y-m-d') . ' ' . $hora_partido;
                // Crear un nuevo partido
                $partido = new Partido();
                $partido->torneo_id = $id;
                $partido->equipo_local_id = $request->equipo_local_id;
                $partido->equipo_visitante_id = $request->equipo_visitante_id;
                $partido->fecha_partido = $fecha_hora_partido;
                $partido->goles_local = $request->goles_local;
                $partido->goles_visitante = $request->goles_visitante;
                $partido->estado = $request->estado_partido ?? 'programado';
                $partido->eventos = $request->eventos;
                $partido->save();
                // Redirigir a la página de torneos con un mensaje de éxito
                return redirect('/admin/torneos/' . $id . '/partidos')->with('success', 'Partido creado correctamente.');
            } else {
                return redirect('/admin/torneos')
                    ->withErrors(['torneo' => 'Torneo no encontrado.'])
                    ->withInput();
            }
        } else {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function eliminarPartido($id)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            $partido = Partido::find($id);
            if ($partido) {
                // Eliminar el partido de la base de datos
                $partido->delete();
                return redirect('/admin/torneos/' . $partido->torneo_id . '/partidos')->with('success', 'Partido eliminado correctamente.');
            } else {
                return redirect('/admin/torneos/' . $partido->torneo_id . '/partidos')->withErrors(['Partido no encontrado.']);
            }
        } else {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function editarPartido(Request $request, $id)
    {
        if (session('admin')) {
            // Validar los datos del formulario
            $validator = Validator::make(
                $request->all(),
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
            if ($validator->fails()) {
                return redirect("/admin/partidos/{$id}")
                    ->withErrors($validator)
                    ->withInput();
            }
            //Buscar el partido por ID
            $partido = Partido::findOrFail($id);
            if ($partido) {
                $torneo = $partido->torneo;
                if (!$torneo) {
                    return redirect('/admin/torneos')
                        ->withErrors(['torneo' => 'Torneo no encontrado.'])
                        ->withInput();
                }
                //Comprobar que la fecha sea posterior a fecha_inicio y anterior a fecha_fin del torneo
                $fecha_partido = Carbon::parse($request->fecha_partido);
                if ($fecha_partido->lt($torneo->fecha_inicio) || $fecha_partido->gt($torneo->fecha_fin)) {
                    return redirect('/admin/partidos/' . $id)
                        ->withErrors(['fecha_partido' => 'La fecha del partido debe estar dentro del rango del torneo.'])
                        ->withInput();
                }
                $hora_partido = $request->hora_partido ?? '00:00:00';
                // Combinar fecha y hora en un solo campo
                $fecha_hora_partido = $fecha_partido->format('Y-m-d') . ' ' . $hora_partido;
                // Actualizar los datos del partido
                $partido->equipo_local_id = $request->equipo_local_id;
                $partido->equipo_visitante_id = $request->equipo_visitante_id;
                $partido->goles_local = $request->goles_local;
                $partido->goles_visitante = $request->goles_visitante;
                $partido->fecha_partido = $fecha_hora_partido;
                $partido->estado = $request->estado;
                $partido->save();

                return redirect("/admin/partidos/{$id}")->with('success', 'Partido actualizado correctamente.');
            }
        }
    }
    public function actualizarResultado(Request $request)
    {
        $partido = Partido::findOrFail($request->partido_id);
        $partido->goles_local = $request->goles_local;
        $partido->goles_visitante = $request->goles_visitante;
        $partido->eventos = $request->eventos_json;
        $partido->save();

        return redirect()->back()->with('success', 'Resultado y eventos guardados correctamente.');
    }

    public function agregarEvento(Request $request, $id)
    {
        $partido = Partido::findOrFail($id);

        $request->validate([
            'eventos' => 'required|array',
        ]);

        $partido->eventos = json_encode($request->eventos);
        $partido->save();

        return response()->json(['status' => 'ok']);
    }
    public function eliminarEvento(Request $request, $id)
    {
        $partido = Partido::findOrFail($id);
        $eventoId = $request->input('id');

        $eventos = $partido->eventos ? json_decode($partido->eventos, true) : [];
        $eventos = array_filter($eventos, fn($e) => $e['id'] != $eventoId);

        $partido->eventos = json_encode(array_values($eventos));
        $partido->save();

        return response()->json(['success' => true]);
    }
}
