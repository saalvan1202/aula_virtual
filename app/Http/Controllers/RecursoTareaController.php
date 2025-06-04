<?php

namespace App\Http\Controllers;

use App\Actions\CrearArchivo;
use App\Models\SeccionRecurso;
use App\Models\CursoDocente;
use App\Models\TipoRecurso;
use App\Models\RecursoTarea;
use App\Models\CursoMatricula;
use App\Models\RecursoTareaEstudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class RecursoTareaController extends Controller
{

    public function index($uuid, $id)
    {

        // Obtener información del curso docente
        $curso_docente = CursoDocente::getInfoCurso($uuid);

        // Obtener información del recurso tarea
        $recurso_tarea = RecursoTarea::where('id', $id)
            ->with('seccionRecurso')
            ->first();

        // Obtener información de los estudiantes y sus tareas
        $estudiantes = CursoMatricula::getInfoEstudiantesCurso($curso_docente->id, $curso_docente->id_curso)->map(function ($estudiante) use ($id) {
            // Obtener las tareas del estudiante relacionadas con la tarea del curso
            $tareas = RecursoTareaEstudiante::where('id_curso_matricula', $estudiante->id_curso_matricula)
                ->where('id_recurso_tarea', $id)
                ->where('estado', 'A')
                ->with('archivos')
                ->get()
                ->sortBy('created_at'); // Ordenar por fecha de creación (ascendente)

            // Agregar las tareas al estudiante
            $estudiante->tareas = $tareas;
            return $estudiante;
        });

        return Inertia::render('VirtualClassroom/Qualifications', [
            'urlRoute' => ltrim(request()->route()->getPrefix(), '/'),
            'recurso_tarea' => $recurso_tarea,
            'estudiantes' => $estudiantes,
            'menu_dependencia'=>menu_dependencia('aula-virtual')
        ]);
    }

    public function store(Request $request,CrearArchivo $create)
    {
        set_time_limit(0);
        $request->validate([
            'id_curso_docente_seccion' => 'required|integer|min:1',
            'id_tipo_recurso' => 'required|integer|min:1',
            'nombre_actividad' => 'required|string|max:150',
            'descripcion_actividad' => 'nullable|string',
            'tipo' => 'nullable|string|max:10',
            'url' => 'required_if:tipo,URL|nullable|url',
            'archivo' => 'required_if:tipo,ARCHIVO|nullable|file',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i',
            'tipo_archivos' => 'nullable|string',
            'numero_intento' => 'required|integer|min:1',
            'puntaje' => 'required|integer|min:1',
        ], [], [
            'nombre_actividad' => 'nombre'
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
            $seccion_recurso->tipo = $tipo;
            $seccion_recurso->url = $request->input('url');
            $seccion_recurso->mostrar = 'A';
            $seccion_recurso->completado = 'I';
            $seccion_recurso->estado = 'A';
            $seccion_recurso->save();

            $id_seccion_recurso = $seccion_recurso->id;


            // Combinar fecha y hora para formar un timestamp
            $timestampInicio = Carbon::createFromFormat(
                'Y-m-d H:i',
                $request->input('fecha_inicio') . ' ' . $request->input('hora_inicio')
            );

            $timestampFin = Carbon::createFromFormat(
                'Y-m-d H:i',
                $request->input('fecha_fin') . ' ' . $request->input('hora_fin')
            );

            // Crear el registro en `recurso_tareas`
            $recurso_tarea = new RecursoTarea();
            $recurso_tarea->id_seccion_recurso = $id_seccion_recurso;
            $recurso_tarea->fecha_inicio = $timestampInicio;
            $recurso_tarea->fecha_fin = $timestampFin;
            $recurso_tarea->tipo_archivos = $request->input('tipo_archivos');
            $recurso_tarea->numero_intento = $request->input('numero_intento');
            $recurso_tarea->grupal = 'N';
            $recurso_tarea->tipo_calificacion = 'SIMPLE';
            $recurso_tarea->puntaje = $request->input('puntaje');
            $recurso_tarea->recuperacion = 'N';
            $recurso_tarea->estado = 'A';
            $recurso_tarea->save();
            if($tipo=='ARCHIVO'){
                $file=$request->file('archivo');
                $seccion=RecursoTarea::find($recurso_tarea->id);
                if($request->hasFile('archivo')){
                    $create->setSubject($seccion)->handle($file);
                }
            }
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

    public function update(Request $request,$id,CrearArchivo $create)
    {
        set_time_limit(0);
        $validatedData = $request->validate([
            'id_curso_docente_seccion' => 'nullable|integer|min:1',
            'id_tipo_recurso' => 'nullable|integer|min:1',
            'nombre_actividad' => 'nullable|string|max:150',
            'descripcion_actividad' => 'nullable|string',
            'tipo' => 'nullable|string|max:10',
            'url' => 'nullable|required_if:tipo,URL|url',
            'archivo' => 'nullable|file',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i',
            'tipo_archivos' => 'nullable|string',
            'numero_intento' => 'nullable|integer|min:1',
            'puntaje' => 'nullable|integer|min:1',
        ]);
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
                'tipo' => $validatedData['tipo'] ?? $seccion_recurso->tipo,
                'url' => $validatedData['url'] ?? $seccion_recurso->url,
            ]);

            $recurso_tarea = RecursoTarea::where('id_seccion_recurso', $id)->first();
            if($seccion_recurso->tipo='ARCHIVO'){
                $file=$request->file('archivo');
                $seccion=$recurso_tarea;
                if($request->hasFile('archivo')){
                    $create->setSubject($seccion)->handle($file);
                }
            }

            if ($recurso_tarea) {
                // Combinar fecha y hora solo si ambas están presentes
                $timestampInicio = isset($validatedData['fecha_inicio']) && isset($validatedData['hora_inicio'])
                    ? Carbon::createFromFormat('Y-m-d H:i', $validatedData['fecha_inicio'] . ' ' . $validatedData['hora_inicio'])
                    : $recurso_tarea->fecha_inicio;

                $timestampFin =isset( $validatedData['fecha_fin']) && isset($validatedData['hora_fin'])
                    ? Carbon::createFromFormat('Y-m-d H:i', $validatedData['fecha_fin'] . ' ' . $validatedData['hora_fin'])
                    : $recurso_tarea->fecha_fin;

                // Actualizar `recurso_tareas`
                $recurso_tarea->update([
                    'fecha_inicio' => $timestampInicio,
                    'fecha_fin' => $timestampFin,
                    'tipo_archivos' => $validatedData['tipo_archivos'] ?? $recurso_tarea->tipo_archivos,
                    'numero_intento' => $validatedData['numero_intento'] ?? $recurso_tarea->numero_intento,
                    'puntaje' => $validatedData['puntaje'] ?? $recurso_tarea->puntaje,
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
}
