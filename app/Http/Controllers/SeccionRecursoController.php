<?php

namespace App\Http\Controllers;

use App\Actions\CrearArchivo;
use App\Models\Archivo;
use App\Models\CursoDocente;
use App\Models\CursoDocenteSeccion;
use App\Models\RecursoForo;
use App\Models\RecursoTarea;
use App\Models\SeccionRecurso;
use App\Models\TipoRecurso;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use PhpParser\Node\Expr\FuncCall;
use Illuminate\Support\Str;

class SeccionRecursoController extends Controller
{

    public function store(Request $request) {
        set_time_limit(0);
        $request->validate([
            'id_curso_docente_seccion' => 'required|integer|min:1',
            'id_tipo_recurso' => 'required|integer|min:1',
            'nombre_actividad' => 'required|string|max:150',
            'descripcion_actividad' => 'nullable|string',
            'tipo' => 'nullable|string|max:10',
            'url' => 'required|url',
        ], [], [
            'nombre_actividad' => 'nombre'
        ]);

        $tipo_recurso=TipoRecurso::find($request->input('id_tipo_recurso'));

        $tipo=$request->input('tipo');
        if($tipo_recurso->nombre=='Link/Url'){
            $tipo='URL';
        }
        if($tipo_recurso->nombre=='Archivo'){
            $tipo='ARCHIVO';
        }

        $seccion_recurso = new SeccionRecurso();
        $seccion_recurso -> id_curso_docente_seccion = $request->input('id_curso_docente_seccion');
        $seccion_recurso -> id_tipo_recurso = $request->input('id_tipo_recurso');
        $seccion_recurso -> nombre = $request->input('nombre_actividad');
        $seccion_recurso -> descripcion = $request->input('descripcion_actividad');
        $seccion_recurso -> tipo = $tipo;
        $seccion_recurso -> url = $request->input('url');
        $seccion_recurso -> mostrar = 'A';
        $seccion_recurso -> completado = 'I';
        $seccion_recurso -> estado = 'A';
        $seccion_recurso -> save();
        return response()->json($seccion_recurso);
    }

    public function update(Request $request, $id)
    {
        set_time_limit(0);

        // Validar datos
        $validatedData = $request->validate([
            'id_curso_docente_seccion' => 'required|integer|min:1',
            'id_tipo_recurso' => 'required|integer|min:1',
            'nombre_actividad' => 'nullable|string|max:150',
            'descripcion_actividad' => 'nullable|string',
            'tipo' => 'nullable|string|max:10',
            'url' => 'nullable|url',
        ], [], [
            'nombre_actividad' => 'nombre'
        ]);

        // Encuentra el curso_docente_seccion
        $seccion_recurso = SeccionRecurso::findOrFail($id);

        // Actualiza los valores
        $seccion_recurso->update([
            'id_curso_docente_seccion' => $seccion_recurso->id_curso_docente_seccion,
            'id_tipo_recurso' => $seccion_recurso->id_tipo_recurso,
            'nombre' => $validatedData['nombre_actividad'] ?? $seccion_recurso->nombre,
            'descripcion' => $validatedData['descripcion_actividad'] ?? $seccion_recurso->descripcion,
            'tipo' => $seccion_recurso->tipo,
            'url' => $validatedData['url'] ?? $seccion_recurso->url,
        ]);

        return response()->json([
            'message' => 'Datos actualizados correctamente',
            'seccion_recurso' => $seccion_recurso
        ]);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Buscar la SeccionRecurso por ID
            $seccion_recurso = SeccionRecurso::findOrFail($id);
            $id_tipo_recurso = $seccion_recurso->id_tipo_recurso;
        
            // Cambiar el estado de SeccionRecurso
            $seccion_recurso->estado = 'I';
            $seccion_recurso->save();
        
            // Cambiar el estado de los recursos relacionados
            if ($id_tipo_recurso == 3) {
                $seccion_recurso->recursoTarea()->update(['estado' => 'I']);
                $seccion_recurso->recursoTarea->recursoTareaEstudiante()->update(['estado' => 'I']);
            }

            if ($id_tipo_recurso == 6) {
                $seccion_recurso->recursoExamen()->update(['estado' => 'I']);
                $recurso_examen = $seccion_recurso->recursoExamen;

                // Actualizar el estado de las preguntas
                $recurso_examen->preguntas()->update(['estado' => 'I']);

                // Actualizar el estado de los exámenes de estudiantes
                $recurso_examen->estudianteExamen()->update(['estado' => 'I']);

                // Actualizar el estado de las alternativas de las preguntas
                $recurso_examen->preguntas()->each(function ($pregunta) {
                    $pregunta->alternativas()->update(['estado' => 'I']);
                });

                // Actualizar el estado de las respuestas de los estudiantes
                $recurso_examen->preguntas()->each(function ($pregunta) {
                    $pregunta->estudianteExamenRespuesta()->update(['estado' => 'I']);
                });     
            }

        // Cambiar el estado de las respuestas de RecursoForo
        if ($id_tipo_recurso == 4) {
            $seccion_recurso->recursoForo()->update(['estado' => 'I']);
            $seccion_recurso->recursoForo->recursoForoRespuesta()->update(['estado' => 'I']);
        }
    
        DB::commit();
        
        // Responder con un mensaje de éxito
        
        return response()->json([
            'message' => 'Actividad y recursos relacionados eliminados correctamente',
            'seccion_recurso' => $seccion_recurso
        ]);

        } catch (\Exception $e) {
        // Revertir la transacción en caso de error
        DB::rollBack();
        return response()->json(['error' => 'Error al eliminar la actividad: ' . $e->getMessage()], 500);
        }
    }

    //FUNCIÓN PARA IMPORTAR ACTIVIDADES DE PERIODOS DE CLASES ANTERIORES
    public function importActividad($id_seccion,$tipo_actividad){
        $actividades=[];
     switch($tipo_actividad){
        case('Tareas'):
            $actividades=RecursoTarea::with('archivos')->selectRaw('secciones_recursos.id,secciones_recursos.nombre,secciones_recursos.descripcion,
            recursos_tareas.fecha_inicio,recursos_tareas.fecha_fin,tp.nombre as tipo_recurso,tp.icono,secciones_recursos.id_tipo_recurso,secciones_recursos.tipo,
            secciones_recursos.url,secciones_recursos.mostrar,secciones_recursos.completado,secciones_recursos.estado,recursos_tareas.*')
            ->join('secciones_recursos','recursos_tareas.id_seccion_recurso','=','secciones_recursos.id')
            ->join('tipos_recursos as tp','tp.id','=','secciones_recursos.id_tipo_recurso')
            ->where('secciones_recursos.id_curso_docente_seccion',$id_seccion)
            ->where('secciones_recursos.estado','A')
            ->get();
            break;
        case('Foros'):
            $actividades=SeccionRecurso::selectRaw('secciones_recursos.id,secciones_recursos.nombre,secciones_recursos.descripcion,
            rt.fecha_inicio,rt.fecha_fin,tp.nombre as tipo_recurso,tp.icono,secciones_recursos.id_tipo_recurso,secciones_recursos.tipo,
            secciones_recursos.url,secciones_recursos.mostrar,secciones_recursos.completado,secciones_recursos.estado,rt.*')
            ->join('recursos_foros as rt','rt.id_seccion_recurso','=','secciones_recursos.id')
            ->join('tipos_recursos as tp','tp.id','=','secciones_recursos.id_tipo_recurso')
            ->where('secciones_recursos.id_curso_docente_seccion',$id_seccion)
            ->where('secciones_recursos.estado','A')
            ->get();
            break;
        case('Link'):
            $actividades=SeccionRecurso::selectRaw('secciones_recursos.id,secciones_recursos.nombre,secciones_recursos.descripcion,
            tp.nombre as tipo_recurso,tp.icono,secciones_recursos.id_tipo_recurso,secciones_recursos.tipo,
            secciones_recursos.url,secciones_recursos.mostrar,secciones_recursos.completado,secciones_recursos.estado')
            ->join('tipos_recursos as tp','tp.id','=','secciones_recursos.id_tipo_recurso')
            ->where('tp.nombre',$tipo_actividad.'/Url')
            ->where('secciones_recursos.id_curso_docente_seccion',$id_seccion)
            ->where('secciones_recursos.estado','A')
            ->get();
            break;
            
     }
     if($tipo_actividad=='Link'){
        foreach($actividades  as $actividad){
            $actividad->hora_inicio='-';
            $actividad->hora_fin='-';
            $actividad->fecha_inicio='-';
            $actividad->fecha_fin='-';
        }
        return response()->json($actividades) ;
     }
     if(!empty($actividades)){
        foreach($actividades  as $actividad){
            $fi=Carbon::parse($actividad->fecha_inicio);
            $ff=Carbon::parse($actividad->fecha_fin);
                    $actividad->hora_inicio=$fi->format('H:i');
                    $actividad->hora_fin=$ff->format('H:i');
                    $actividad->fecha_inicio=$fi->format('Y-m-d');
                    $actividad->fecha_fin=$ff->format('Y-m-d');
    }
}
    return response()->json($actividades);
}
public function storeMigrateActividades(Request $request,CrearArchivo $create){
    DB::beginTransaction();
    try{
        foreach($request['recursos'] as $recurso){

            $tipo_recurso = TipoRecurso::find($recurso['id_tipo_recurso']);
            $tipo = $recurso['tipo'];

            if ($tipo_recurso->nombre == 'Link/Url') {
                $tipo = 'URL';
            }
            if ($tipo_recurso->nombre == 'Archivo') {
                $tipo = 'ARCHIVO';
            }

            // Crear el registro en `seccion_recursos`
            $seccion_recurso = new SeccionRecurso();
            $seccion_recurso->id_curso_docente_seccion = $request['seccion_curso_docente'];
            $seccion_recurso->id_tipo_recurso = $recurso['id_tipo_recurso'];
            $seccion_recurso->nombre = $recurso['nombre'];
            $seccion_recurso->descripcion = $recurso['descripcion'];
            $seccion_recurso->tipo = $tipo;
            $seccion_recurso->url = $recurso['url'];
            $seccion_recurso->mostrar = 'A';
            $seccion_recurso->completado = 'I';
            $seccion_recurso->estado = 'A';
            $seccion_recurso->save();

            if($recurso['tipo_recurso']!='Link/Url'){
                // Combinar fecha y hora para formar un timestamp
                $timestampInicio = Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $recurso['fecha_inicio'] . ' ' . $recurso['hora_inicio']
                );

                $timestampFin = Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $recurso['fecha_fin'] . ' ' . $recurso['hora_fin']
                );
            }

            switch($recurso['tipo_recurso']){
                case('Tareas'):
                    $recurso_tarea = new RecursoTarea();
                    $recurso_tarea->id_seccion_recurso = $seccion_recurso->id;
                    $recurso_tarea->fecha_inicio = $timestampInicio;
                    $recurso_tarea->fecha_fin = $timestampFin;
                    $recurso_tarea->tipo_archivos = $recurso['tipo_archivos'];
                    $recurso_tarea->numero_intento = $recurso['numero_intento'];
                    $recurso_tarea->grupal = $recurso['grupal'];
                    $recurso_tarea->tipo_calificacion = $recurso['tipo_calificacion'];
                    $recurso_tarea->puntaje = $recurso['puntaje'];
                    $recurso_tarea->recuperacion = $recurso['recuperacion'];
                    $recurso_tarea->estado = $recurso['estado'];
                    $recurso_tarea->save();
                    if($tipo=='ARCHIVO'){
                        $archivo=new Archivo();
                        $archivo->uuid=(string)Str::uuid();
                        $archivo->nombre=$recurso['archivos'][0]['nombre'];
                        $archivo->archivo=$recurso['archivos'][0]['archivo'];
                        $archivo->bytes=$recurso['archivos'][0]['bytes'];
                        $archivo->id_detalle=0;
                        $archivo->codigo=null;
                        $archivo->referencia= $recurso['archivos'][0]['referencia'];
                        $archivo->id_referencia=$recurso_tarea->id;
                        $archivo->estado='A';
                        $archivo->save();
                    }
                    break;
                case('Foros'):
                    $recurso_foro = new RecursoForo();
                    $recurso_foro->id_seccion_recurso = $seccion_recurso->id;
                    $recurso_foro->fecha_inicio = $timestampInicio;
                    $recurso_foro->fecha_fin = $timestampFin;
                    $recurso_foro->maximo_comentarios = $recurso['maximo_comentarios'];
                    $recurso_foro->modo_calificacion = $recurso['modo_calificacion'];
                    $recurso_foro->puntaje = $recurso['puntaje'];
                    $recurso_foro->semana = $recurso['semana'];
                    $recurso_foro->estado = $recurso['estado'];
                    $recurso_foro->save();
        
                    break;
                case('Examenes'):
                    
                    break;
            }
        }
        DB::commit();
        return response()->json('ok');
    }
    catch(\Exception $e){
        DB::rollBack();
        return response()->json([
            'error' => 'Tuvimos un error al guardar los datos.',
            'message' => $e->getMessage(),
        ], 500);
    }
}
}
