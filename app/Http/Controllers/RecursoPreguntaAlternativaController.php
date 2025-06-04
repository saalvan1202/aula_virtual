<?php

namespace App\Http\Controllers;

use App\Models\RecursoExamenPregunta;
use App\Models\RecursoPreguntaAlternativa;
use App\Services\ExamenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecursoPreguntaAlternativaController extends Controller
{

    public function store($id_pregunta, Request $request, ExamenService $examen_service)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'id_recurso_examen_pregunta' => 'required|exists:recursos_examenes_preguntas,id',
            'descripcion' => 'required|string',
            'porcentaje' => 'required|numeric|between:0.00,100.00|regex:/^\d{1,3}(\.\d{1,2})?$/',
            'correcta' => 'required|string|in:S,N',
        ]);

        $seccion_recurso = RecursoExamenPregunta::getSeccionRecurso($id_pregunta);
        $id_recurso_examen = $seccion_recurso->id_recurso_examen;

        if ($seccion_recurso->mostrar == 'A') {
            return response()->json(['error' => 'El examen debe estar deshabilitado para modificar alternativas.'], 400);
        }

        DB::beginTransaction();
        try {

            $pregunta = RecursoExamenPregunta::findOrFail($id_pregunta);

            if ($pregunta->id_tipo_pregunta == 1 && $validatedData['correcta'] == 'S') {
                RecursoPreguntaAlternativa::where('id_recurso_examen_pregunta', $validatedData['id_recurso_examen_pregunta'])
                    ->update(['correcta' => 'N', 'porcentaje' => '0.00']);
            }

            // Crear una nueva alternativa
            $recursoPreguntaAlternativa = new RecursoPreguntaAlternativa();
            $recursoPreguntaAlternativa->id_recurso_examen_pregunta = $validatedData['id_recurso_examen_pregunta'];
            $recursoPreguntaAlternativa->descripcion = $validatedData['descripcion'];
            $recursoPreguntaAlternativa->porcentaje = $validatedData['porcentaje'];
            $recursoPreguntaAlternativa->correcta = $validatedData['correcta'];
            $recursoPreguntaAlternativa->estado = 'A';
            $recursoPreguntaAlternativa->save();

            $response = [
                'message' => 'Alternativa creada correctamente.',
                'recurso_pregunta_alternativa' => $recursoPreguntaAlternativa
            ];

            if ($pregunta->id_tipo_pregunta == 1) {
                $recalcular_examen = $examen_service->recalificarExamenes($id_recurso_examen);

                if (!empty($recalcular_examen['examenes_recalificados'])) {
                    $response['examenes_recalculados'] = $recalcular_examen['examenes_recalificados'];
                }
            }

            // Confirmar la transacción
            DB::commit();
            return response()->json($response, 200);

        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar los porcentajes'], 500);
        }
    }
    
    public function updateCheck($id_pregunta, Request $request, ExamenService $examen_service) 
    {
        $seccion_recurso = RecursoExamenPregunta::getSeccionRecurso($id_pregunta);
        $id_recurso_examen = $seccion_recurso->id_recurso_examen;

        if ($seccion_recurso->mostrar == 'A') {
            return response()->json(['error' => 'El examen debe estar deshabilitado para modificar alternativas.'], 400);
        
        }

        DB::beginTransaction();
        try {

            $pregunta = RecursoExamenPregunta::findOrFail($id_pregunta);

            $recurso_pregunta_alternativa = RecursoPreguntaAlternativa::findOrFail($request->id);
            $recurso_pregunta_alternativa->correcta = $request->value;
            $recurso_pregunta_alternativa->porcentaje = '100.00';
            
            if ($request->value == 'N') {
                $recurso_pregunta_alternativa->porcentaje = '0.00';
            }

            if ($pregunta->id_tipo_pregunta == 1 && $request->value == 'S') {
                RecursoPreguntaAlternativa::where('id_recurso_examen_pregunta', $recurso_pregunta_alternativa->id_recurso_examen_pregunta)->where('estado', 'A')
                    ->update(['correcta' => 'N', 'porcentaje' => '0.00']);
            }
            
            $recurso_pregunta_alternativa->save();

            $recalcular_examen = $examen_service->recalificarExamenes($id_recurso_examen);

            $response = [
                'message' => 'Alternativa actualizada correctamente.',
            ];

            if (!empty($recalcular_examen['examenes_recalificados'])) {
                $response['examenes_recalculados'] = $recalcular_examen['examenes_recalificados'];
            }

            // Confirmar la transacción
            DB::commit();
            return response()->json($response, 200);

        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar la alternativa'], 500);
        }
    }

    public function updatePorcentajes($id_pregunta, Request $request, ExamenService $examen_service)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'porcentajes' => 'required|array',
            'porcentajes.*.id' => 'required|integer|exists:recursos_preguntas_alternativas,id',
            'porcentajes.*.porcentaje' => [
                'required',
                'numeric',
                'min:0.01', // Evita que el porcentaje sea 0.00
                'max:100',
                'regex:/^\d+(\.\d{1,2})?$/' // Máximo 2 decimales
            ],
        ]);

        // Obtener la sección del recurso asociada a la pregunta
        $seccion_recurso = RecursoExamenPregunta::getSeccionRecurso($id_pregunta);
        $id_recurso_examen = $seccion_recurso->id_recurso_examen;

        // Verificar si el examen está habilitado
        if ($seccion_recurso->mostrar == 'A') {
            return response()->json(['error' => 'El examen debe estar deshabilitado para actualizar los porcentajes.'], 400);
        }

        // Obtener el tipo de pregunta
        $pregunta = RecursoExamenPregunta::findOrFail($id_pregunta);
        $id_tipo_pregunta = $pregunta->id_tipo_pregunta;

        // Validar que las preguntas de opción múltiple tengan más de una alternativa
        if ($id_tipo_pregunta == 2 && count($validatedData['porcentajes']) < 2) {
            return response()->json([
                'error' => 'Las preguntas de opción múltiple deben contar con más de una alternativa correcta.'
            ], 400);
        }

        // Calcular la suma total de los porcentajes
        $totalPorcentaje = collect($validatedData['porcentajes'])
            ->sum('porcentaje');

        // Validar que la suma de los porcentajes sea exactamente 100.00
        if (bccomp($totalPorcentaje, '100.00', 2) !== 0) {
            return response()->json([
                'error' => 'La suma total de los porcentajes debe ser exactamente 100.00.'
            ], 400);
        }

        // Iniciar una transacción de base de datos
        DB::beginTransaction();
        try {

            if (empty($validatedData['porcentajes'])) {
                return response()->json(['message' => 'No hay porcentajes para actualizar.'], 400);
            }

            // Actualizar los porcentajes de las alternativas
            foreach ($validatedData['porcentajes'] as $item) {
                RecursoPreguntaAlternativa::where('id', $item['id'])
                    ->update(['porcentaje' => $item['porcentaje']]);
            }

            $response = [
                'message' => 'Porcentajes actualizados correctamente.',
            ];

            $recalcular_examen = $examen_service->recalificarExamenes($id_recurso_examen);

            if (!empty($recalcular_examen['examenes_recalificados'])) {
                $response['examenes_recalculados'] = $recalcular_examen['examenes_recalificados'];
            }

            // Confirmar la transacción
            DB::commit();
            return response()->json($response, 200);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar los porcentajes'], 500);
        }
    }

    public function updateDescripcion($id_pregunta, Request $request)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:recursos_preguntas_alternativas,id',
            'descripcion' => 'required|string',
        ]);

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
            RecursoPreguntaAlternativa::where('id', $validatedData['id'])
                ->update(['descripcion' => $validatedData['descripcion']]);

            // Confirmar la transacción
            DB::commit();
            return response()->json(['message' => 'Alternativa actualizada correctamente'], 200);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar la alternativa'], 500);
        }
    }

    public function changeCorrect(Request $request, ExamenService $examen_service) 
    {
        $validatedData = $request->validate([
            'id_pregunta' => 'required|integer|exists:recursos_examenes_preguntas,id',
        ]);

        $id_pregunta = $validatedData['id_pregunta'];

        $seccion_recurso = RecursoExamenPregunta::getSeccionRecurso($id_pregunta);
        $id_recurso_examen = $seccion_recurso->id_recurso_examen;

        if ($seccion_recurso->mostrar == 'A') {
            return response()->json(['error' => 'El examen debe estar deshabilitado para modificar alternativas.'], 400);
        
        }
        $pregunta = RecursoExamenPregunta::findOrFail($id_pregunta);

        if ($pregunta->id_tipo_pregunta != 3) {
            return response()->json(['error' => 'Solo se puede realizar la acción con las preguntas del tipo verdadero/falso'], 400);
        }

        $alternativas = RecursoPreguntaAlternativa::where('id_recurso_examen_pregunta', $id_pregunta)->get();

        DB::beginTransaction();
        try {

            foreach ($alternativas as $alternativa) {
                if ($alternativa->correcta == 'S') {
                    $alternativa->correcta = 'N';
                    $alternativa->porcentaje = '0.00';
                } else {
                    $alternativa->correcta = 'S';
                    $alternativa->porcentaje = '100.00';
                }
                $alternativa->save();
            }

            $recalcular_examen = $examen_service->recalificarExamenes($id_recurso_examen);

            $response = [
                'message' => 'Alternativa actualizada correctamente.',
            ];

            if (!empty($recalcular_examen['examenes_recalificados'])) {
                $response['examenes_recalculados'] = $recalcular_examen['examenes_recalificados'];
            }

            // Confirmar la transacción
            DB::commit();

            return response()->json($response, 200);
            
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar los porcentajes'], 500);
        }
    }

    public function destroy($id_pregunta, $id_alternativa, ExamenService $examen_service)
    {
        DB::beginTransaction();
        try {
            $seccion_recurso = RecursoExamenPregunta::getSeccionRecurso($id_pregunta);
            $id_recurso_examen = $seccion_recurso->id_recurso_examen;

            if ($seccion_recurso->mostrar == 'A') {
                return response()->json(['error' => 'El examen debe estar deshabilitado para elimnar alternativas.'], 400);
            }

            // Buscar la SeccionRecurso por ID
            $recurso_pregunta_alternativa = RecursoPreguntaAlternativa::findOrFail( $id_alternativa);

            // Cambiar el estado de SeccionRecurso
            $recurso_pregunta_alternativa->estado = 'I';
            $recurso_pregunta_alternativa->save();

            $recurso_pregunta_alternativa->estudianteExamenRespuesta()->update(['estado' => 'I']);

            $response = [
                'message' => 'Alternativa eliminada correctamente',
                'recurso_pregunta_alternativa' => $recurso_pregunta_alternativa
            ];

            $recalcular_examen = $examen_service->recalificarExamenes($id_recurso_examen);

            if (!empty($recalcular_examen['examenes_recalificados'])) {
                $response['examenes_recalculados'] = $recalcular_examen['examenes_recalificados'];
            }

            DB::commit();

            // Responder con un mensaje de éxito
            return response()->json($response, 200);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar la alternativa: ' . $e->getMessage()], 500);
        }
    }
}
