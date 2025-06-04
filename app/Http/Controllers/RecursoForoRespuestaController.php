<?php

namespace App\Http\Controllers;

use App\Models\RecursoForo;
use App\Models\RecursoForoRespuesta;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecursoForoRespuestaController extends Controller
{
    public function store(Request $request) {
        set_time_limit(0);
    
        // Validar los inputs
        $request->validate([
            'id_curso_matricula' => 'required|integer|min:1',
            'id_recurso_foro' => 'required|integer|min:1',
            'descripcion' => 'required|string',
            'url' => 'nullable|string',
        ], [], []);
    
        // Obtener el recurso foro
        $recurso_foro = RecursoForo::find($request->input('id_recurso_foro'));
    
        // Validar si el foro está disponible
        $fechaActual = Carbon::now();
    
        // Comprobar si la fecha de inicio del foro es posterior a la fecha actual
        if ($fechaActual->lessThan(Carbon::parse($recurso_foro->fecha_inicio))) {
            return response()->json([
                'error' => 'El foro aún no está disponible.'
            ], 400);
        }
    
        // Comprobar si la fecha de fin del foro ha pasado
        if ($fechaActual->greaterThan(Carbon::parse($recurso_foro->fecha_fin))) {
            return response()->json([
                'error' => 'El foro ha caducado.'
            ], 400);
        }

        $id_curso_matricula = $request->input('id_curso_matricula');

        // Verificar si el usuario ya tiene una respuesta calificada
        $respuestaCalificada = RecursoForoRespuesta::where('id_curso_matricula', $id_curso_matricula)
            ->where('id_recurso_foro', $request->input('id_recurso_foro'))
            ->where('estado_calificacion', 'CALIFICADO')
            ->where('estado', 'A')
            ->exists();

        if ($respuestaCalificada) {
            return response()->json([
                'error' => 'Su respuesta ya fue calificada.'
            ], 400);
        }
    
        // Verificar cuántos comentarios ha realizado el estudiante en el foro
        $comentariosRealizados = RecursoForoRespuesta::where('id_curso_matricula', $id_curso_matricula)
            ->where('id_recurso_foro', $request->input('id_recurso_foro'))
            ->where('estado', 'A')
            ->count();
    
        // Comprobar si el estudiante ha alcanzado el máximo de comentarios
        if ($comentariosRealizados >= $recurso_foro->maximo_comentarios) {
            return response()->json([
                'error' => 'No hay más intentos de comentar disponibles.'
            ], 400);
        }
    
        // Crear el registro en `seccion_recursos`
        $recurso_foro_respuesta = new RecursoForoRespuesta();
        $recurso_foro_respuesta->id_recurso_foro = $request->input('id_recurso_foro');
        $recurso_foro_respuesta->id_curso_matricula = $id_curso_matricula;
        $recurso_foro_respuesta->id_padre = 0;
        $recurso_foro_respuesta->puntaje = 0;
        $recurso_foro_respuesta->descripcion = $request->input('descripcion');
    
        $url = $request->input('url');
    
        if ($url) {
            $recurso_foro_respuesta->tipo = 'URL';
            $recurso_foro_respuesta->url = $url;
        }
    
        $recurso_foro_respuesta->estado = 'A';
        $recurso_foro_respuesta->save();
    
        return response()->json($recurso_foro_respuesta);
    }

    public function updateNotas(Request $request)
    {
      
        try {
            $validatedData = $request->validate([
                'calificaciones' => 'required|array',
                'calificaciones.*.id' => 'required|integer|exists:recursos_foros_respuestas,id',
                'calificaciones.*.estado_calificacion' => 'required|string',
                'calificaciones.*.puntaje' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/|between:0.00,20.00',
            ]);

            DB::beginTransaction();
            
            foreach ($validatedData['calificaciones'] as $calificacion) {
                
                $updateData = [
                    'estado_calificacion' => $calificacion['estado_calificacion'],
                    'puntaje' => $calificacion['puntaje'],
                    'fecha_calificacion' => Carbon::now(),
                ];
    
                if ($calificacion['estado_calificacion'] === 'PENDIENTE') {
                    $updateData['puntaje'] = '0.00';
                    $updateData['fecha_calificacion'] = null;
                }
    
                RecursoForoRespuesta::where('id', $calificacion['id'])
                    ->update($updateData);
            }

            DB::commit();

            return response()->json(['message' => 'Calificaciones actualizadas con éxito.']);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Hubo un error al actualizar las calificaciones.'], 500);
        }
    }

    public function destroy($id) {
        $recurso_foro_respuesta = RecursoForoRespuesta::findOrFail($id);
    
        $recurso_foro_respuesta->estado_calificacion = 'PENDIENTE';
        $recurso_foro_respuesta->puntaje = '0.00';
        $recurso_foro_respuesta->fecha_calificacion = null;
        $recurso_foro_respuesta->estado = 'I';
        $recurso_foro_respuesta->save();
    
        return response()->json([
            'message' => 'Comentario eliminado correctamente',
            'curso_docente_seccion' => $recurso_foro_respuesta
        ]);
    }    

}

