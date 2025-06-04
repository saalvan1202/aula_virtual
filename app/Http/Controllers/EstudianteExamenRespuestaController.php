<?php

namespace App\Http\Controllers;

use App\Models\EstudianteExamenRespuesta;
use App\Traits\ValidatesExam;
use App\Services\ExamenService;
use App\Models\RecursoExamenPregunta;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;

class EstudianteExamenRespuestaController extends Controller
{
    use ValidatesExam;
    
    public function store(Request $request, ExamenService $examen_service) 
    {
        set_time_limit(0);

        // Validación
        $validated = $request->validate([
            'id_curso_matricula' => 'required|integer|min:1',
            'id_recurso_examen' => 'required|integer|min:1',
            'fecha_inicio' => 'required|date_format:Y-m-d H:i:s',
            'answers' => 'required|array',
            'answers.*.id_answers' => 'nullable|array',
            'answers.*.id_pregunta' => 'required|integer|min:1|exists:recursos_examenes_preguntas,id',
            'answers.*.id_tipo_pregunta' => 'required|integer|min:1|exists:tipos_preguntas,id',
            'answers.*.text_answer' => 'nullable|string',
            'answers.*.id_answers.*' => 'nullable|integer',
        ]);

        $id_curso_matricula = $validated['id_curso_matricula'];
        $id_recurso_examen = $validated['id_recurso_examen'];
        $fecha_inicio = $validated['fecha_inicio'];

        $validacion_examen = $this->validarExamen($id_recurso_examen);
        if ($validacion_examen instanceof JsonResponse) {
            return $validacion_examen;
        }

        DB::transaction(function () use ($validated, $id_curso_matricula, $id_recurso_examen, $examen_service, $fecha_inicio) {

            $validacion_examen_enviado = $this->validarExamenEnviado($id_curso_matricula, $id_recurso_examen, true, $fecha_inicio);

            if ($validacion_examen_enviado instanceof JsonResponse) {
                throw new Exception($validacion_examen_enviado->getData()->error);
            }

            $resultados = $examen_service->procesarRespuestas($validated, $validacion_examen_enviado);

            $validacion_examen_enviado = $examen_service->calcularPuntajeTotal(
                $resultados,
                $validacion_examen_enviado,
            );

            return response()->json([
                'message' => 'Respuestas registradas correctamente.',
                'examen' => $validacion_examen_enviado,
            ], 200);
        });

        return response()->json([
            'message' => 'Respuestas registradas correctamente.',
        ], 200);
    }

    public function updateScoreText(Request $request, ExamenService $examen_service)
    {
        $validated = $request->validate([
            'id_pregunta' => 'required|integer|min:1|exists:recursos_examenes_preguntas,id',
            'id_respuesta' => 'required|integer|min:1|exists:estudiantes_examenes_respuestas,id',
            'id_estudiante_examen' => 'required|integer|min:1|exists:estudiantes_examenes,id',
            'puntaje' => 'required|numeric|between:0.00,20.00|regex:/^\d{1,2}(\.\d{1,2})?$/',
        ]);

        $id_pregunta = $validated['id_pregunta'];
        $id_respuesta = $validated['id_respuesta'];
        $id_estudiante_examen = $validated['id_estudiante_examen'];
        $puntaje_respuesta = $validated['puntaje'];

        $pregunta = RecursoExamenPregunta::findOrFail($id_pregunta);
        $puntaje_pregunta = $pregunta->puntaje;

        $puntaje_pregunta = (float) $puntaje_pregunta;
        $puntaje_respuesta = (float) $puntaje_respuesta;

        if ($puntaje_pregunta < $puntaje_respuesta) {
            return response()->json(['error' => "El puntaje asignado debe ser igual o menor que $puntaje_pregunta."], 400);
        }

        DB::transaction(function () use ($id_respuesta, $id_estudiante_examen, $puntaje_respuesta, $examen_service) {
            $respuesta = EstudianteExamenRespuesta::findOrFail($id_respuesta);

            $puntaje_respuesta = number_format($puntaje_respuesta, 2, '.', '');

            $respuesta->puntaje = $puntaje_respuesta;
            $respuesta->calificado = 'S';
            $respuesta->save();

            $estudiante_examen = $examen_service->procesarRespuestasById($id_estudiante_examen);

            $calcular_puntaje_total = $examen_service->calcularPuntajeTotal(
                $estudiante_examen['respuestas_examen'],
                $estudiante_examen['estudiante_examen'],
            );

            return response()->json([
                'message' => 'Respuestas calificada correctamente.',
                'examen' => $calcular_puntaje_total,
            ], 200);
        });
    }
}