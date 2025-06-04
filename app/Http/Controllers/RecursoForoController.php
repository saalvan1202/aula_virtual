<?php

namespace App\Http\Controllers;

use App\Models\RecursoForo;
use App\Models\RecursoForoRespuesta;
use App\Models\SeccionRecurso;
use App\Models\TipoRecurso;
use App\Models\CursoDocente;
use App\Models\CursoMatricula;
use App\Services\Variable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RecursoForoController extends Controller
{
    public function index($uuid, $id)
    {
        $perfil=auth()->user()->id_perfil;
        if(!Variable::isTeacher($perfil) && !Variable::isStudent($perfil)){
            abort('403','No autorizado');
        }

        if(Variable::isTeacher($perfil)){
            $foro = RecursoForo::where('id', $id)
                ->where('estado', 'A')
                ->with('seccionRecurso')
                ->first();

            $comentarios = RecursoForoRespuesta::where('id_recurso_foro', $id)->where('estado', 'A')->get()->map(function ($comentario) {
                $info_estudiante = RecursoForo::getInfoEstudiantes($comentario->id);
                return [
                    'comentario' => $comentario,
                    'estudiante' => $info_estudiante,
                ];
            });


            return Inertia::render('VirtualClassroom/Forum', [
                'urlRoute' => ltrim(request()->route()->getPrefix(), '/'),
                'foro' => $foro,
                'menu_dependencia'=>menu_dependencia('aula-virtual'),
                'comentarios' => $comentarios
            ]);
        } elseif (Variable::isStudent($perfil)) {
            $foro = RecursoForo::where('id', $id)
                ->where('estado', 'A')
                ->with('seccionRecurso')
                ->first();

            $curso = CursoDocente::getInfoCursoEstudiante($uuid);

            // Recuperar los comentarios
            $comentarios = RecursoForoRespuesta::where('id_recurso_foro', $id)
                ->where('estado', 'A')
                ->get()
                ->map(function ($comentario) {
                    // Obtener la información del estudiante
                    $infoEstudiante = RecursoForo::getInfoEstudiantes($comentario->id);

                    // Verificar si $infoEstudiante es null
                    if (!$infoEstudiante) {
                        // Si es null, crear un objeto vacío para evitar el error
                        $infoEstudiante = new \stdClass();
                    }

                    // Obtener los datos del foro
                    $foro_max = RecursoForo::find($comentario->id_recurso_foro);
                    $maximoComentarios = $foro_max->maximo_comentarios;

                    // Contar los comentarios realizados por el estudiante en el foro
                    $comentariosRealizados = RecursoForoRespuesta::where('id_recurso_foro', $comentario->id_recurso_foro)
                        ->where('id_curso_matricula', $infoEstudiante->id_curso_matricula)
                        ->where('estado', 'A')
                        ->count();

                    // Asignar el campo 'puede_comentar' al objeto infoEstudiante
                    $infoEstudiante->puede_comentar = $comentariosRealizados < $maximoComentarios;

                    // Retornar el comentario y la información del estudiante
                    return [
                        'comentario' => $comentario,
                        'estudiante' => $infoEstudiante,
                    ];
                });

            return Inertia::render('VirtualClassroom/Student/Forum', [
                'urlRoute' => ltrim(request()->route()->getPrefix(), '/'),
                'foro' => $foro,
                'comentarios' => $comentarios,
                'id_usuario_estudiante' => auth()->user()->id,
                'id_curso_matricula' => $curso->id_curso_matricula,
                'menu_dependencia'=>menu_dependencia('aula-virtual')
            ]);
        }

    }

    public function calificar($uuid, $id)
    {
        // Obtener información del curso docente
        $curso_docente = CursoDocente::getInfoCurso($uuid);

        // Obtener información del recurso tarea
        $recurso_foro = RecursoForo::where('id', $id)
            ->with('seccionRecurso')
            ->first();

        // Obtener información de los estudiantes y sus tareas
        $estudiantes = CursoMatricula::getInfoEstudiantesCurso($curso_docente->id, $curso_docente->id_curso)->map(function ($estudiante) use ($id) {
            // Obtener las tareas del estudiante relacionadas con la tarea del curso
            $respuestas = RecursoForoRespuesta::where('id_curso_matricula', $estudiante->id_curso_matricula)
                ->where('id_recurso_foro', $id)
                ->where('estado', 'A')
                ->get()
                ->sortBy('created_at'); // Ordenar por fecha de creación (ascendente)

            // Agregar las tareas al estudiante
            $estudiante->respuestas = $respuestas;

            return $estudiante;
        });

        return Inertia::render('VirtualClassroom/QualificationsForum', [
            'urlRoute' => ltrim(request()->route()->getPrefix(), '/'),
            'recurso_foro' => $recurso_foro,
            'estudiantes' => $estudiantes,
            'menu_dependencia'=>menu_dependencia('aula-virtual')
        ]);
    }

    public function store(Request $request) {
        set_time_limit(0);

        $request->validate([
            'id_curso_docente_seccion' => 'required|integer|min:1',
            'id_tipo_recurso' => 'required|integer|min:1',
            'nombre_actividad' => 'required|string|max:150',
            'descripcion_actividad' => 'nullable|string',
            'fecha_inicio_foro' => 'required|date',
            'fecha_fin_foro' => 'required|date',
            'hora_inicio_foro' => 'required|date_format:H:i',
            'hora_fin_foro' => 'required|date_format:H:i',
            'modo_calificacion'=>'required|string|in:MAXIMA,PROMEDIO',
            'maximo_comentarios' => 'required|integer|min:1',
            'semana' => 'required|integer|min:1',
            'puntaje' => 'required|integer|min:1',
        ], ['semana'=>'Ingrese semana'], [
            'nombre_actividad' => 'nombre',
            'fecha_inicio_foro' => 'fecha de inicio',
            'fecha_fin_foro' => 'fecha de fin',
            'hora_inicio_foro' => 'hora de inicio',
            'hora_fin_foro' => 'hora de fin',
            'modo_calificacion'=>'modo de calificacion',
            'maximo_comentarios' => 'maximo de comentarios',
        ]);

        // Iniciar la transacción
        DB::beginTransaction();

        try {
            $tipo_recurso = TipoRecurso::find($request->input('id_tipo_recurso'));
            $tipo = $request->input('tipo');

            if ($tipo_recurso->nombre == 'Link/Url') {
                $tipo = 'URL';
            }
            if ($tipo_recurso->nombre == 'Archivo') {
                $tipo = 'ARCHIVO';
            }

            // Crear el registro en `seccion_recursos`
            $seccion_recurso = new SeccionRecurso();
            $seccion_recurso->id_curso_docente_seccion = $request->input('id_curso_docente_seccion');
            $seccion_recurso->id_tipo_recurso = $request->input('id_tipo_recurso');
            $seccion_recurso->nombre = $request->input('nombre_actividad');
            $seccion_recurso->descripcion = $request->input('descripcion_actividad');
            $seccion_recurso->mostrar = 'A';
            $seccion_recurso->completado = 'I';
            $seccion_recurso->estado = 'A';
            $seccion_recurso->save();

            $id_seccion_recurso = $seccion_recurso->id;

            // Combinar fecha y hora para formar un timestamp
            $timestampInicio = Carbon::createFromFormat(
                'Y-m-d H:i',
                $request->input('fecha_inicio_foro') . ' ' . $request->input('hora_inicio_foro')
            );

            $timestampFin = Carbon::createFromFormat(
                'Y-m-d H:i',
                $request->input('fecha_fin_foro') . ' ' . $request->input('hora_fin_foro')
            );

            // Crear el registro en `recurso_tareas`
            $recurso_foro = new RecursoForo();
            $recurso_foro->id_seccion_recurso = $id_seccion_recurso;
            $recurso_foro->fecha_inicio = $timestampInicio;
            $recurso_foro->fecha_fin = $timestampFin;
            $recurso_foro->semana = $request->input('semana');
            $recurso_foro->modo_calificacion = $request->input('modo_calificacion');
            $recurso_foro->puntaje = $request->input('puntaje');
            $recurso_foro->maximo_comentarios = $request->input('maximo_comentarios');
            $recurso_foro->estado = 'A';
            $recurso_foro->save();

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

        $request->validate([
            'id_curso_docente_seccion' => 'nullable|integer|min:1',
            'id_tipo_recurso' => 'nullable|integer|min:1',
            'nombre_actividad' => 'nullable|string|max:150',
            'descripcion_actividad' => 'nullable|string',
            'fecha_inicio_foro' => 'nullable|date',
            'fecha_fin_foro' => 'nullable|date',
            'hora_inicio_foro' => 'nullable|date_format:H:i',
            'hora_fin_foro' => 'nullable|date_format:H:i',
            'modo_calificacion' => 'nullable|string|in:MAXIMA,PROMEDIO',
            'maximo_comentarios' => 'nullable|integer|min:1',
            'semana' => 'nullable|integer|min:1',
            'puntaje' => 'nullable|integer|min:1',
        ], [], [
            'nombre_actividad' => 'nombre',
            'fecha_inicio_foro' => 'fecha de inicio',
            'fecha_fin_foro' => 'fecha de fin',
            'hora_inicio_foro' => 'hora de inicio',
            'hora_fin_foro' => 'hora de fin',
            'modo_calificacion' => 'modo de calificacion',
            'maximo_comentarios' => 'maximo de comentarios'
        ]);

        // Iniciar la transacción
        DB::beginTransaction();

        try {
            // Buscar el recurso existente
            $seccion_recurso = SeccionRecurso::findOrFail($id);

            // Actualizar solo los campos enviados
            $seccion_recurso->update(array_filter([
                'id_curso_docente_seccion' => $request->input('id_curso_docente_seccion', $seccion_recurso->id_curso_docente_seccion),
                'id_tipo_recurso' => $request->input('id_tipo_recurso', $seccion_recurso->id_tipo_recurso),
                'nombre' => $request->input('nombre_actividad', $seccion_recurso->nombre),
                'descripcion' => $request->input('descripcion_actividad', $seccion_recurso->descripcion),
            ]));

            // Buscar el recurso del foro relacionado
            $recurso_foro = RecursoForo::where('id_seccion_recurso', $id)->firstOrFail();

            // Combinar fecha y hora solo si ambas se envían
            $timestampInicio = $request->has(['fecha_inicio_foro', 'hora_inicio_foro'])
                ? Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $request->input('fecha_inicio_foro', Carbon::parse($recurso_foro->fecha_inicio)->format('Y-m-d')) . ' ' .
                    $request->input('hora_inicio_foro', Carbon::parse($recurso_foro->fecha_inicio)->format('H:i'))
                )
                : Carbon::parse($recurso_foro->fecha_inicio);

            $timestampFin = $request->has(['fecha_fin_foro', 'hora_fin_foro'])
                ? Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $request->input('fecha_fin_foro', Carbon::parse($recurso_foro->fecha_fin)->format('Y-m-d')) . ' ' .
                    $request->input('hora_fin_foro', Carbon::parse($recurso_foro->fecha_fin)->format('H:i'))
                )
                : Carbon::parse($recurso_foro->fecha_fin);


            // Actualizar solo los campos enviados del foro
            $recurso_foro->update(array_filter([
                'fecha_inicio' => $timestampInicio,
                'fecha_fin' => $timestampFin,
                'semana' => $request->input('semana', $recurso_foro->semana),
                'modo_calificacion' => $request->input('modo_calificacion', $recurso_foro->modo_calificacion),
                'puntaje' => $request->input('puntaje', $recurso_foro->puntaje),
                'maximo_comentarios' => $request->input('maximo_comentarios', $recurso_foro->maximo_comentarios),
            ]));

            // Confirmar la transacción
            DB::commit();

            return response()->json([
                'message' => 'Datos actualizados correctamente',
                'seccion_recurso' => $seccion_recurso,
            ]);
        } catch (\Exception $e) {
            // Revertir la transacción si ocurre un error
            DB::rollBack();
            return response()->json([
                'error' => 'Ocurrió un error al actualizar los datos.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
