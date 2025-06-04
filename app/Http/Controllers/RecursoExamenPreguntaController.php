<?php

namespace App\Http\Controllers;

use App\Models\RecursoExamen;
use App\Models\RecursoExamenPregunta;
use App\Models\RecursoPreguntaAlternativa;
use App\Models\SeccionRecurso;
use App\Services\ExamenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecursoExamenPreguntaController extends Controller
{

    public function store(Request $request) 
    {
        set_time_limit(0);

        $request->validate([
            'id_recurso_examen' => 'required|integer|min:1',
            'id_tipo_pregunta' => 'required|integer|min:1',
            'descripcion' => 'required|string',
            'contestacion' => 'required_if:id_tipo_pregunta,3|integer|in:1,2',

            'simple_selection_alternativas' => 'nullable|required_if:id_tipo_pregunta,1|array',
            'simple_selection_alternativas.*.descripcion' => 'nullable|required_if:id_tipo_pregunta,1|string',
            'simple_selection_alternativas.*.correcta' => 'nullable|required_if:id_tipo_pregunta,1|boolean',
            'simple_selection_alternativas.*.porcentaje' => 'nullable|required_if:id_tipo_pregunta,1|numeric|between:0.0000,100.0000|regex:/^\d{1,3}(\.\d{1,4})?$/',

            'multi_selection_alternativas' => 'nullable|required_if:id_tipo_pregunta,2|array',
            'multi_selection_alternativas.*.descripcion' => 'nullable|required_if:id_tipo_pregunta,2|string',
            'multi_selection_alternativas.*.correcta' => 'nullable|required_if:id_tipo_pregunta,2|boolean',
            'multi_selection_alternativas.*.porcentaje' => 'nullable|required_if:id_tipo_pregunta,2|numeric|between:0.0000,100.0000|regex:/^\d{1,3}(\.\d{1,4})?$/',

        ], [], [
            'id_tipo_pregunta' => 'Tipo de pregunta',
            'contestacion' => 'Respuesta correcta'
        ]);

        $id_recurso_examen = $request->input('id_recurso_examen');
        $seccion_recurso = RecursoExamen::getSeccionRecurso($id_recurso_examen);

        if ($seccion_recurso->mostrar == 'A') {
            return response()->json(['error' => 'El examen debe estar deshabilitado para agregar preguntas.'], 400);
        }

        // Iniciar la transacción
        DB::beginTransaction();

        try {

            // Crear el registro en `seccion_recursos`
            $recurso_examen_pregunta = new RecursoExamenPregunta();
            $recurso_examen_pregunta->id_recurso_examen = $id_recurso_examen;
            $recurso_examen_pregunta->id_tipo_pregunta = $request->input('id_tipo_pregunta');
            $recurso_examen_pregunta->descripcion = $request->input('descripcion');
            $recurso_examen_pregunta->duracion = 0;
            $recurso_examen_pregunta->puntaje = '0.00';
            $recurso_examen_pregunta->estado = 'A';
            $recurso_examen_pregunta->save();

            $id_recurso_examen_pregunta = $recurso_examen_pregunta->id;

            if ($recurso_examen_pregunta->id_tipo_pregunta == 1) {
                $alternativas = $request->input('simple_selection_alternativas', []);
            
                if (!is_array($alternativas) || count($alternativas) < 2) {
                    DB::rollBack();
                    return response()->json([
                        'error' => 'Debe haber al menos 2 alternativas en la selección simple.'
                    ], 400);
                }

                // Validar que haya solo una alternativa correcta
                $correctas = array_filter($alternativas, function($alt) {
                    return $alt['correcta'];
                });

                if (count($correctas) !== 1) {
                    DB::rollBack();
                    return response()->json([
                        'error' => 'Debe haber exactamente 1 alternativa correcta en la selección simple.'
                    ], 400);
                }
            
                foreach ($alternativas as $alt) {
                    $alternativa = new RecursoPreguntaAlternativa();
                    $alternativa->id_recurso_examen_pregunta = $id_recurso_examen_pregunta;
                    $alternativa->descripcion = $alt['descripcion'];
                    $alternativa->porcentaje = $alt['correcta'] ? '100.00' : '0.00';
                    $alternativa->correcta = $alt['correcta'] ? 'S' : 'N';
                    $alternativa->estado = 'A';
                    $alternativa->save();
                }
            } elseif ($recurso_examen_pregunta->id_tipo_pregunta == 2) {
                $alternativas = $request->input('multi_selection_alternativas', []);
            
                if (!is_array($alternativas) || count($alternativas) < 2) {
                    DB::rollBack();
                    return response()->json([
                        'error' => 'Debe haber al menos 2 alternativas en la selección múltiple.'
                    ], 400);
                }

                $correctas = array_filter($alternativas, function($alt) {
                    return $alt['correcta'];
                });

                if (count($correctas) < 2) {
                    DB::rollBack();
                    return response()->json([
                        'error' => 'Debe haber al menos 2 alternativas correctas en la selección múltiple.'
                    ], 400);
                }

                // Validar que los porcentajes sumen 1.00
                $totalPorcentaje = array_reduce($alternativas, function($carry, $alt) {
                    return $carry + $alt['porcentaje'];
                }, 0.00);

                if (bccomp($totalPorcentaje, '0.00', 2) !== 0) {
                    DB::rollBack();
                    return response()->json([
                        'error' => 'La suma de los porcentajes debe ser 0.00.'
                    ], 400);
                }
            
                foreach ($alternativas as $alt) {
                    $alternativa = new RecursoPreguntaAlternativa();
                    $alternativa->id_recurso_examen_pregunta = $id_recurso_examen_pregunta;
                    $alternativa->descripcion = $alt['descripcion'];
                    $alternativa->porcentaje = '0.00';
                    $alternativa->correcta = $alt['correcta'] ? 'S' : 'N';
                    $alternativa->estado = 'A';
                    $alternativa->save();
                }
            } else if ($recurso_examen_pregunta->id_tipo_pregunta == 3) {
                // Si el tipo de pregunta es 3 (Verdadero/Falso), crear las alternativas 
                // Obtener la respuesta correcta desde la solicitud
                $respuestaCorrecta = $request->input('contestacion');

                // Alternativa "Verdadero"
                $alternativaVerdadero = new RecursoPreguntaAlternativa();
                $alternativaVerdadero->id_recurso_examen_pregunta = $id_recurso_examen_pregunta;
                $alternativaVerdadero->descripcion = 'VERDADERO';
                $alternativaVerdadero->porcentaje = $respuestaCorrecta == 1 ? '100.00' : '0.00';
                $alternativaVerdadero->correcta = $respuestaCorrecta == 1 ? 'S' : 'N';
                $alternativaVerdadero->estado = 'A';
                $alternativaVerdadero->save();

                // Alternativa "Falso"
                $alternativaFalso = new RecursoPreguntaAlternativa();
                $alternativaFalso->id_recurso_examen_pregunta = $id_recurso_examen_pregunta;
                $alternativaFalso->descripcion = 'FALSO';
                $alternativaFalso->porcentaje = $respuestaCorrecta == 2 ? '100.00' : '0.00'; 
                $alternativaFalso->correcta = $respuestaCorrecta == 2 ? 'S' : 'N';
                $alternativaFalso->estado = 'A';
                $alternativaFalso->save();
            }

            // Confirmar la transacción
            DB::commit();

            return response()->json($recurso_examen_pregunta);
        } catch (\Exception $e) {
            // Revertir la transacción si ocurre un error
            DB::rollBack();
            return response()->json([
                'error' => 'Ocurrió un error al guardar los datos.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updatePuntajes(Request $request, ExamenService $examen_service)
    {
        $validatedData = $request->validate([
            'puntajes' => 'required|array',
            'puntajes.*.id' => 'required|integer|exists:recursos_examenes_preguntas,id',
            'puntajes.*.puntaje' => [
                'required',
                'numeric',
                'min:0.01', // Evita que el puntaje sea 0.00
                'max:20',
                'regex:/^\d+(\.\d{1,2})?$/' // Máximo 2 decimales
            ],
            'id_seccion_recurso' => 'required|integer|min:1'
        ]);

        $id_seccion_recurso = $validatedData['id_seccion_recurso'];

        $query = SeccionRecurso::with([
            'recursoExamen' => function ($query) {
                $query->where('estado', 'A')
                    ->select([
                        'id',
                        'id_seccion_recurso',
                        'fecha_inicio',
                        'fecha_fin',
                        'estado',
                    ]);
            }
        ]);

        $seccion_recurso = $query->findOrFail($id_seccion_recurso);

        $id_recurso_examen = $seccion_recurso->recursoExamen->id;

        if ($seccion_recurso->mostrar == 'A') {
            return response()->json(['error' => 'El examen debe estar deshabilitado para actualizar los puntajes.'], 400);
        }

        $totalPuntaje = collect($validatedData['puntajes'])
            ->sum('puntaje');


        if (bccomp($totalPuntaje, '20.00', 2) !== 0 ) {
            return response()->json([
                'error' => 'La suma total de los puntajes debe ser exactamente 20.00.'
            ], 400);
        }
        
        DB::beginTransaction();
        try {
            // Validar si hay puntajes para actualizar
            if (empty($validatedData['puntajes'])) {
                return response()->json(['message' => 'No hay puntajes para actualizar.'], 400);
            }

            // Actualizar los puntajes de las preguntas
            foreach ($validatedData['puntajes'] as $item) {
                RecursoExamenPregunta::where('id', $item['id'])
                    ->update(['puntaje' => $item['puntaje']]);
            }

            $response = [
                'message' => 'Puntajes actualizados correctamente.',
            ];

            $recalcular_examen = $examen_service->recalificarExamenes($id_recurso_examen);

            if (!empty($recalcular_examen['examenes_recalificados'])) {
                $response['examenes_recalculados'] = $recalcular_examen['examenes_recalificados'];
            }
            
            DB::commit();

            return response()->json($response, 200);
        } catch (\Exception $e) {
            // Rollback en caso de error
            DB::rollBack();

            // Retornar respuesta de error
            return response()->json(['error' => 'Error al actualizar los puntajes.'], 500);
        }
    }

    public function updateQuestion(Request $request)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:recursos_examenes_preguntas,id',
            'descripcion' => 'required|string',
        ]);

        $id_pregunta = $validatedData['id'];

        // Obtener la sección del recurso asociada a la pregunta
        $seccion_recurso = RecursoExamenPregunta::getSeccionRecurso($id_pregunta);

        // Verificar si el examen está habilitado
        if ($seccion_recurso->mostrar == 'A') {
            return response()->json(['error' => 'El examen debe estar deshabilitado para actualizar los porcentajes.'], 400);
        }

        // Iniciar una transacción de base de datos
        DB::beginTransaction();
        try {
            // Actualizar la descripción de la alternativa
            RecursoExamenPregunta::where('id', $id_pregunta)
                ->update(['descripcion' => $validatedData['descripcion']]);

            // Confirmar la transacción
            DB::commit();
            return response()->json(['message' => 'Pregunta actualizada correctamente'], 200);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar la pregunta'], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $seccion_recurso = RecursoExamenPregunta::getSeccionRecurso($id);

            if ($seccion_recurso->mostrar == 'A') {
                return response()->json(['error' => 'El examen debe estar deshabilitado para elimnar preguntas.'], 400);
            }

            // Buscar la SeccionRecurso por ID
            $recurso_examen_pregunta = RecursoExamenPregunta::findOrFail($id);
        
            // Cambiar el estado de SeccionRecurso
            $recurso_examen_pregunta->estado = 'I';
            $recurso_examen_pregunta->save();
        
            $recurso_examen_pregunta->alternativas()->update(['estado' => 'I']);
            $recurso_examen_pregunta->estudianteExamenRespuesta()->update(['estado' => 'I']);

            DB::commit();
        
            // Responder con un mensaje de éxito
            return response()->json([
                'message' => 'Pregunta y alternativas relacionadas eliminados correctamente',
                'recurso_examen_pregunta' => $recurso_examen_pregunta
            ]);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar la pregunta: ' . $e->getMessage()], 500);
        }
    }
    
}
