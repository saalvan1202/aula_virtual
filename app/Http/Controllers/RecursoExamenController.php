<?php

namespace App\Http\Controllers;

use App\Models\SeccionRecurso;
use App\Models\CursoDocente;
use App\Models\CursoMatricula;
use App\Models\EstudianteExamen;
use App\Models\RecursoExamen;
use App\Models\TipoPregunta;
use App\Services\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class RecursoExamenController extends Controller
{

    public function index($uuid, $id)
    {
        $id_perfil = auth()->user()->id_perfil;

        if(Variable::isStudent($id_perfil)){
            abort(403, 'No tiene permisos para acceder a esta página.');
        }

        // Obtener información del curso docente
        $curso_docente = CursoDocente::getInfoCurso($uuid);

        $recurso_examen = RecursoExamen::getRecursoExamenConPreguntas($id, $id_perfil);

        // Calcular la suma de los puntajes de las preguntas
        $suma_puntaje = $recurso_examen->preguntas->sum(function ($pregunta) {
            return (float) $pregunta->puntaje;
        });

        // Formatear la suma de los puntajes
        $new_suma_puntaje = number_format($suma_puntaje, 2, '.', '');

        // Verificar si alguna pregunta tiene puntaje 0
        $search_zero = $recurso_examen->preguntas->contains(function ($pregunta) {
            return (float) $pregunta->puntaje === 0.00;
        });

        // Verificar si hay alerta (puntaje total no es 20 o alguna pregunta tiene puntaje 0)
        $verify_alert = $search_zero || $new_suma_puntaje != 20.00;

        // Obtener tipos de pregunta activos
        $tipo_pregunta = TipoPregunta::where('estado', 'A')->get();

        // Recorrer las preguntas y agregar los campos "incompleto" y "suma_porcentaje"
        $preguntas_con_incompleto = $recurso_examen->preguntas->map(function ($pregunta) {
            // Inicializar el campo suma_porcentaje
            $pregunta->suma_porcentaje = 0.00;

            // Verificar si el id_tipo_pregunta es igual a 2
            if ($pregunta->id_tipo_pregunta == 1) {
                // Asigna true si hay menos de 2 alternativas
                $validar_cantidad = $pregunta->alternativas->count() < 2;
                // valida si hay exactamente una respuesta correcta
                $validar_respuesta = $pregunta->alternativas->where('correcta', 'S')->count() !== 1;
                // Agregar el campo "incompleto" a la pregunta
                $pregunta->incompleto = $validar_cantidad || $validar_respuesta;

            } elseif ($pregunta->id_tipo_pregunta == 2) {
                // Calcular la suma de los porcentajes de las alternativas
                $suma_porcentajes = $pregunta->alternativas->sum(function ($alternativa) {
                    return (float) $alternativa->porcentaje;
                });

                // Asignar la suma de los porcentajes al campo suma_porcentaje
                $pregunta->suma_porcentaje = number_format($suma_porcentajes, 2, '.', '');

                // Verificar si alguna alternativa tiene porcentaje 0
                $tiene_alternativa_incompleta = false;

                foreach ($pregunta->alternativas as $alternativa) {
                    if ((float) $alternativa->porcentaje === 0.00 && $alternativa->correcta === 'S') {
                        $tiene_alternativa_incompleta = true;
                        break; // Salir del bucle tan pronto como se encuentre un 0
                    }
                }

                // Verificar si la suma de los porcentajes es diferente de 100
                $suma_incorrecta = $pregunta->suma_porcentaje != 100.00;

                // Agregar el campo "incompleto" a la pregunta
                $pregunta->incompleto = $tiene_alternativa_incompleta || $suma_incorrecta;
            } else {
                // Si no es tipo 2, asignar false directamente al campo "incompleto"
                $pregunta->incompleto = false;
            }

            return $pregunta;
        });

        return Inertia::render('VirtualClassroom/Exam', [
            'urlRoute' => ltrim(request()->route()->getPrefix(), '/'),
            'curso_docente' => $curso_docente,
            'tipo_pregunta' => $tipo_pregunta,
            'recurso_examen' => $recurso_examen,
            'verify_alert' => $verify_alert,
            'suma_puntaje' => $suma_puntaje,
            'preguntas' => $preguntas_con_incompleto, // Pasar las preguntas con los nuevos campos
            'menu_dependencia'=>menu_dependencia('aula-virtual')
        ]);
    }

    public function solveExam($uuid, $id)
    {
        $id_perfil = auth()->user()->id_perfil;

        if( Variable::isTeacher($id_perfil) ){
            abort(403, 'No tiene permisos para acceder a esta página.');
        }

        $recurso_examen = RecursoExamen::getRecursoExamenConPreguntas($id, $id_perfil);

        $fecha_actual = Carbon::now();

        // Comprobar si la fecha de inicio del foro es posterior a la fecha actual
        if ($fecha_actual->lessThan(Carbon::parse($recurso_examen->fecha_inicio))) {
            abort(403, 'El examen aún no está disponible.');
        }

        if ($fecha_actual->greaterThan(Carbon::parse($recurso_examen->fecha_fin))) {
            abort(403, 'El examen ha caducado.');
        }

        if ($recurso_examen->seccionRecurso->mostrar == 'I') {
            abort(403, 'El examen está deshabilitado');
        }

        // Obtener información del curso docente
        $curso_docente = CursoDocente::getInfoCursoEstudiante($uuid);

        return Inertia::render('VirtualClassroom/Student/SolveExam', [
            'urlRoute' => ltrim(request()->route()->getPrefix(), '/'),
            'curso_docente' => $curso_docente,
            'recurso_examen' => $recurso_examen,
            'menu_dependencia'=>menu_dependencia('aula-virtual')
        ]);
    }

    public function calificar($uuid, $id)
    {
        // Obtener información del curso docente
        $curso_docente = CursoDocente::getInfoCurso($uuid);

        // Obtener información del recurso tarea
        $recurso_examen = RecursoExamen::where('id', $id)
            ->with('seccionRecurso')
            ->with(['preguntas' => function ($query) {
                $query->where('estado', 'A'); // Solo preguntas activas
            }])
            ->first();

        // Obtener información de los estudiantes y sus tareas
        $estudiantes = CursoMatricula::getInfoEstudiantesCurso($curso_docente->id, $curso_docente->id_curso)->map(function ($estudiante) use ($id) {
            // Obtener las tareas del estudiante relacionadas con la tarea del curso
            $examen = EstudianteExamen::where('id_curso_matricula', $estudiante->id_curso_matricula)
                ->where('id_recurso_examen', $id)
                ->where('estado', 'A')
                ->get()
                ->sortBy('created_at'); // Ordenar por fecha de creación (ascendente)

            // Agregar las tareas al estudiante
            $estudiante->examen = $examen;

            return $estudiante;
        });

        return Inertia::render('VirtualClassroom/QualificationsExam', [
            'urlRoute' => ltrim(request()->route()->getPrefix(), '/'),
            'recurso_examen' => $recurso_examen,
            'estudiantes' => $estudiantes,
            'uuid' => $uuid,
            'menu_dependencia'=>menu_dependencia('aula-virtual')
        ]);
    }

    public function calificarManualmente($uuid, $id, $id_curso_matricula)
    {
        $id_perfil = auth()->user()->id_perfil;

        $component = '';
        $curso_docente = [];

        if (Variable::isStudent($id_perfil)) {
            $component = 'VirtualClassroom/Student/Exam';
            $curso_docente = CursoDocente::getInfoCursoEstudiante($uuid);
        } else {
            $component = 'VirtualClassroom/ManualQualification';
            $curso_docente = CursoDocente::getInfoCurso($uuid);
        }

        // Obtener información del curso docente
        $info_estudiante = CursoMatricula::getInfoEstudianteMatricula($id_curso_matricula);

        $recurso_examen = RecursoExamen::getRecursoExamenConPreguntas($id, $id_perfil, true);

        if (Variable::isStudent($id_perfil)) {
            $fecha_actual = Carbon::now(); // Obtener la fecha y hora actual
            $fecha_fin = Carbon::parse($recurso_examen->fecha_fin); // Convertir a objeto Carbon

            // Verificar si la hora actual es menor que fecha_inicio o mayor que fecha_fin
            if ($fecha_actual->lt($fecha_fin)) {
                abort(403, 'No puedes acceder a las respuestas fuera del horario permitido.');
            }
        }

        $respuestas_estudiante = EstudianteExamen::where('id_recurso_examen', $id)
            ->where('id_curso_matricula', $id_curso_matricula)
            ->with(['estudianteExamenRespuesta'])
            ->first();

        // Agregar estado_examen al recurso_examen
        $recurso_examen->estado_examen = $respuestas_estudiante->estado_examen ?? 'PENDIENTE';
        $recurso_examen->puntaje_examen = $respuestas_estudiante->puntaje;
        $recurso_examen->id_estudiante_examen = $respuestas_estudiante->id;

        // Agregar respuestas y calcular puntaje_obtenido a cada pregunta
        foreach ($recurso_examen->preguntas as $pregunta) {
            $pregunta->respuestas = $respuestas_estudiante->estudianteExamenRespuesta
                ->where('id_recurso_examen_pregunta', $pregunta->id)
                ->where('estado', 'A')
                ->values(); // Reindexar array

            // Calcular puntaje obtenido
            $puntajes = $pregunta->respuestas->pluck('puntaje')->map(fn($p) => (float) $p);
            $pregunta->puntaje_obtenido = number_format($puntajes->contains(0.00) ? 0.00 : $puntajes->sum(), 2, '.', '');
        }

        return Inertia::render($component, [
            'urlRoute' => ltrim(request()->route()->getPrefix(), '/'),
            'curso_docente' => $curso_docente,
            'recurso_examen' => $recurso_examen,
            'preguntas' => $recurso_examen->preguntas, // Incluye las preguntas con respuestas y puntaje obtenido
            'info_estudiante' => $info_estudiante,
            'menu_dependencia'=>menu_dependencia('aula-virtual')
        ]);
    }

    public function store(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'id_curso_docente_seccion' => 'required|integer|min:1',
            'id_tipo_recurso' => 'required|integer|min:1',
            'nombre_actividad' => 'required|string|max:150',
            'descripcion_actividad' => 'nullable|string',
            'fecha_inicio_examen' => 'required|date',
            'hora_inicio_examen' => 'required|date_format:H:i',
            'duracion_hora' => 'required|integer|min:0',
            'duracion_minuto' => 'required|integer|min:0',
            'semana' => 'required|integer|min:1',
            // 'numero_intento' => 'required|integer|min:1',
            // 'barajar' => 'required|string|in:S,N',
            // 'tiempo_pregunta' => 'required|string|in:S,N',
        ], [], [
            'nombre_actividad' => 'nombre'
        ]);

        if ($request->input('duracion_hora') == 0 && $request->input('duracion_minuto') == 0) {
            return response()->json([
                'error' => 'Al menos uno de los campos "duracion_hora" o "duracion_minuto" debe tener un valor mayor que 0.',
            ], 422); // 422 es el código de estado para errores de validación
        }

        // Iniciar la transacción
        DB::beginTransaction();

        try {

            // Crear el registro en `seccion_recursos`
            $seccion_recurso = new SeccionRecurso();
            $seccion_recurso->id_curso_docente_seccion = $request->input('id_curso_docente_seccion');
            $seccion_recurso->id_tipo_recurso = $request->input('id_tipo_recurso');
            $seccion_recurso->nombre = $request->input('nombre_actividad');
            $seccion_recurso->descripcion = $request->input('descripcion_actividad');
            $seccion_recurso->mostrar = 'I';
            $seccion_recurso->completado = 'I';
            $seccion_recurso->estado = 'A';
            $seccion_recurso->save();

            $id_seccion_recurso = $seccion_recurso->id;

            // Combinar fecha y hora para formar un timestamp
            $timestampInicio = Carbon::createFromFormat(
                'Y-m-d H:i',
                $request->input('fecha_inicio_examen') . ' ' . $request->input('hora_inicio_examen')
            );

            // Calcular la duración en minutos
            $duracion_hora = $request->input('duracion_hora');
            $duracion_minuto = $request->input('duracion_minuto');
            $duracion_total_minutos = ($duracion_hora * 60) + $duracion_minuto;

            $timestampFin = $timestampInicio->copy()->addMinutes($duracion_total_minutos);

            // Crear el registro en `recurso_tareas`
            $recurso_examen = new RecursoExamen();
            $recurso_examen->id_seccion_recurso = $id_seccion_recurso;
            $recurso_examen->fecha_inicio = $timestampInicio;
            $recurso_examen->fecha_fin = $timestampFin;
            $recurso_examen->duracion = $duracion_total_minutos; // Guardar la duración en minutos
            $recurso_examen->semana = $request->input('semana');
            $recurso_examen->numero_intento = 1;
            $recurso_examen->barajar = 'N';
            $recurso_examen->tiempo_pregunta = 'N';
            $recurso_examen->recuperacion = 'N';
            $recurso_examen->estado = 'A';
            $recurso_examen->save();

            // Confirmar la transacción
            DB::commit();

            return response()->json($seccion_recurso);
        } catch (\Exception $e) {
            // Revertir la transacción si ocurre un error
            DB::rollBack();
            return response()->json([
                'error' => 'Ocurrió un error al guardar los datos.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        set_time_limit(0);

        $validatedData = $request->validate([
            'id_curso_docente_seccion' => 'required|integer|min:1',
            'id_tipo_recurso' => 'required|integer|min:1',
            'nombre_actividad' => 'required|string|max:150',
            'descripcion_actividad' => 'nullable|string',
            'fecha_inicio_examen' => 'required|date',
            'hora_inicio_examen' => 'required|date_format:H:i',
            'duracion_hora' => 'required|integer|min:0',
            'duracion_minuto' => 'required|integer|min:0',
            'semana' => 'required|integer|min:1',
            // 'numero_intento' => 'required|integer|min:1',
            // 'barajar' => 'required|string|in:S,N',
            // 'tiempo_pregunta' => 'required|string|in:S,N',
        ]);

        if ($request->input('duracion_hora') == 0 && $request->input('duracion_minuto') == 0) {
            return response()->json([
                'error' => 'Al menos uno de los campos "duracion_hora" o "duracion_minuto" debe tener un valor mayor que 0.',
            ], 422); // 422 es el código de estado para errores de validación
        }

        // Iniciar la transacción
        DB::beginTransaction();

        try {
            $seccion_recurso = SeccionRecurso::findOrFail($id);

            // Actualizar `seccion_recursos`
            $seccion_recurso->update([
                'id_curso_docente_seccion' => $validatedData['id_curso_docente_seccion'] ?? $seccion_recurso->id_curso_docente_seccion,
                'id_tipo_recurso' => $validatedData['id_tipo_recurso'] ?? $seccion_recurso->id_tipo_recurso,
                'nombre' => $validatedData['nombre_actividad'] ?? $seccion_recurso->nombre,
                'descripcion' => $validatedData['descripcion_actividad'] ?? $seccion_recurso->descripcion,
            ]);

            $recurso_examen = RecursoExamen::where('id_seccion_recurso', $id)->first();

            if ($recurso_examen) {
                // Combinar fecha y hora solo si ambas están presentes
                $timestampInicio = $validatedData['fecha_inicio_examen'] && $validatedData['hora_inicio_examen']
                    ? Carbon::createFromFormat('Y-m-d H:i', $validatedData['fecha_inicio_examen'] . ' ' . $validatedData['hora_inicio_examen'])
                    : $recurso_examen->fecha_inicio;

                // Calcular la duración en minutos
                $duracion_hora = $request->input('duracion_hora');
                $duracion_minuto = $request->input('duracion_minuto');
                $duracion_total_minutos = ($duracion_hora * 60) + $duracion_minuto;

                $timestampFin = $timestampInicio->copy()->addMinutes($duracion_total_minutos);

                // Actualizar `recurso_tareas`
                $recurso_examen->update([
                    'fecha_inicio' => $timestampInicio,
                    'fecha_fin' => $timestampFin,
                    'duracion' => $duracion_total_minutos,
                    'semana' => $validatedData['semana'] ?? $recurso_examen->semana,
                    // 'numero_intento' => $validatedData['numero_intento'] ?? $recurso_examen->numero_intento,
                    // 'barajar' => $validatedData['barajar'] ?? $recurso_examen->barajar,
                    // 'tiempo_pregunta' => $validatedData['tiempo_pregunta'] ?? $recurso_examen->tiempo_pregunta,
                ]);
            }

            // Confirmar la transacción
            DB::commit();

            return response()->json($seccion_recurso);
        } catch (\Exception $e) {
            // Revertir la transacción si ocurre un error
            DB::rollBack();
            return response()->json([
                'error' => 'Ocurrió un error al actualizar los datos.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function disableExam($id_seccion_recurso)
    {
        $seccion_recurso = SeccionRecurso::findOrFail($id_seccion_recurso);

        $seccion_recurso->mostrar = 'I';
        $seccion_recurso->save();

        return response()->json([
            'message' => 'El examen fue deshabilitado correctamente',
            'recurso_examen_pregunta' => $seccion_recurso
        ]);
    }

    public function enableExam($id_recurso_examen)
    {
        $recurso_examen = RecursoExamen::where('id', $id_recurso_examen)
            ->with([
                'preguntas' => function ($query) {
                    $query->where('estado', 'A')
                        ->orderBy('created_at', 'asc')
                        ->select(
                            'id',
                            'id_recurso_examen',
                            'id_tipo_pregunta',
                            'descripcion',
                            'duracion',
                            'puntaje',
                        )->with([
                            'alternativas' => function ($query) {
                                $query->where('estado', 'A')
                                    ->select(
                                        'id',
                                        'id_recurso_examen_pregunta',
                                        'descripcion',
                                        'porcentaje',
                                        'correcta',
                                    );
                            },
                        ]);
                },
            ])
            ->first();

        // Obtener las preguntas
        $preguntas = $recurso_examen->preguntas;

        // Validación 1: Verificar si hay alguna pregunta con puntaje 0.00
        foreach ($preguntas as $pregunta) {
            if ($pregunta->puntaje == 0.00) {
                return response()->json([
                    'error' => 'Todas las preguntas deben tener puntajes asignados.'
                ], 400);
            }
        }

        // Validación 2: Verificar que la suma total de puntajes sea 20.00
        $suma_puntajes = $preguntas->sum('puntaje');

        if (bccomp($suma_puntajes, '20.00', 2) !== 0) {
            return response()->json([
                'error' => 'La suma total de los puntajes debe ser exactamente 20.00.'
            ], 400);
        }

        // Validación 3: Verificar condiciones para preguntas de tipo 2
        foreach ($preguntas as $pregunta) {
            // En las preguntas de tipo 1, valida si existen 2 alternativas o mas y tambien valida que haya solo una respuesta correcto
            if ($pregunta->id_tipo_pregunta == 1) {
                if ($pregunta->alternativas->count() < 2) {
                    return response()->json([
                        'error' => 'Las preguntas de selección simple deben tener al menos dos alternativas.'
                    ], 400);
                }

                $alternativas_correctas = $pregunta->alternativas->filter(function ($alternativa) {
                    return $alternativa->correcta === 'S';
                });

                if ($alternativas_correctas->count() !== 1) {
                    return response()->json([
                        'error' => 'Las preguntas de selección simple deben tener exactamente una alternativa correcta.'
                    ], 400);
                }
            }
            if ($pregunta->id_tipo_pregunta == 2) {
                // Condición A: Verificar si alguna alternativa correcta tiene porcentaje 0.00
                $condicion_a = $pregunta->alternativas->contains(function ($alternativa) {
                    return $alternativa->correcta && (float) $alternativa->porcentaje === 0.00;
                });

                // Condición B: Verificar si la suma de los porcentajes es diferente de 100.00
                $suma_porcentajes = $pregunta->alternativas->sum(function ($alternativa) {
                    return (float) $alternativa->porcentaje;
                });
                $condicion_b = bccomp($suma_porcentajes, '100.00', 2) !== 0;

                // Si ambas condiciones son true, devolver error
                if ($condicion_a && $condicion_b) {
                    return response()->json([
                        'error' => 'Las preguntas de opción multiple no pueden tener alternativas correctas con porcentaje 0.00 y la suma de los porcentajes debe ser exactamente 100.00.'
                    ], 400);
                }
            }
        }

        // Habilitar el examen
        $id_seccion_recurso = $recurso_examen->id_seccion_recurso;
        $seccion_recurso = SeccionRecurso::findOrFail($id_seccion_recurso);
        $seccion_recurso->mostrar = 'A';
        $seccion_recurso->save();

        return response()->json([
            'message' => 'El examen fue habilitado correctamente.',
            'recurso_examen_pregunta' => $seccion_recurso
        ]);
    }
}