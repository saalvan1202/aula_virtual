<?php

namespace App\Services;

use App\Models\EstudianteExamenRespuesta;
use App\Models\RecursoExamenPregunta;
use App\Models\EstudianteExamen;
use App\Dtos\RespuestaExamen;
use App\Dtos\Respuesta;
use Carbon\Carbon;
use Exception;

class ExamenService
{

    public function procesarRespuestas($validated, $validacion_examen_enviado)
    {
        $respuestas_examen = [];

        foreach ($validated['answers'] as $answer) {
            if ($answer['id_tipo_pregunta'] == 4) {
                // Pregunta tipo texto
                EstudianteExamenRespuesta::create([
                    'id_estudiante_examen' => $validacion_examen_enviado->id,
                    'id_recurso_examen_pregunta' => $answer['id_pregunta'],
                    'respuesta' => $answer['text_answer'],
                    'calificado' => 'N',
                    'puntaje' => '0.00',
                    'estado' => 'A',
                ]);

                $respuestas_examen[] = new RespuestaExamen(
                    $answer['id_pregunta'],
                    [new Respuesta(null, '0.00', 'N')],
                    '0.00'
                );
            } else {
                // Pregunta con alternativas
                $pregunta = RecursoExamenPregunta::with('alternativas')->findOrFail($answer['id_pregunta']);
                $puntaje_pregunta = $pregunta->puntaje;

                $respuestas_pregunta = [];
                $puntaje_obtenido_total = 0;

                foreach ($answer['id_answers'] as $id_answer) {
                    $alternativa = $pregunta->alternativas->firstWhere('id', $id_answer);

                    if (!$alternativa || $alternativa->estado !== 'A') {
                        throw new Exception('La alternativa no existe o no está activa.');
                    }

                    $porcentaje = number_format(($alternativa->porcentaje / 100), 4, '.', '');
                    $puntaje_obtenido = $porcentaje * $puntaje_pregunta;
                    $puntaje_obtenido_formateado = number_format($puntaje_obtenido, 4, '.', '');

                    EstudianteExamenRespuesta::create([
                        'id_estudiante_examen' => $validacion_examen_enviado->id,
                        'id_recurso_examen_pregunta' => $answer['id_pregunta'],
                        'id_recurso_pregunta_alternativa' => $id_answer,
                        'calificado' => 'S',
                        'puntaje' => $puntaje_obtenido_formateado,
                        'estado' => 'A',
                    ]);

                    $respuestas_pregunta[] = new Respuesta(
                        $id_answer,
                        $puntaje_obtenido_formateado,
                        'S'
                    );

                    // Sumar el puntaje obtenido
                    $puntaje_obtenido_total += $puntaje_obtenido;
                }

                $respuestas_examen[] = new RespuestaExamen(
                    $answer['id_pregunta'],
                    $respuestas_pregunta,
                    number_format($puntaje_obtenido_total, 2, '.', '') // Total de la pregunta
                );
            }
        }

        return $respuestas_examen;
    }

    public function procesarRespuestasById($id_estudiante_examen) 
    {
        $query = EstudianteExamen::with([
            'estudianteExamenRespuesta' => function ($query) {
                $query->where('estado', 'A')
                    ->select([
                        'id_estudiante_examen',
                        'id_recurso_examen_pregunta',
                        'id_recurso_pregunta_alternativa',
                        'respuesta',
                        'calificado',
                        'puntaje',
                        'estado'
                    ]);
            }
        ]);
        
        $estudiante_examen = $query->findOrFail($id_estudiante_examen);

        // Agrupar respuestas por pregunta
        $respuestas_por_pregunta = [];
        foreach ($estudiante_examen->estudianteExamenRespuesta as $respuesta) {
            $id_pregunta = $respuesta->id_recurso_examen_pregunta;

            if (!isset($respuestas_por_pregunta[$id_pregunta])) {
                $respuestas_por_pregunta[$id_pregunta] = [
                    'respuestas' => [],
                    'puntaje_total' => 0.00,
                ];
            }

            // Crear el DTO Respuesta
            $respuesta_dto = new Respuesta(
                $respuesta->id_recurso_pregunta_alternativa,
                $respuesta->puntaje,
                $respuesta->calificado
            );

            $respuestas_por_pregunta[$id_pregunta]['respuestas'][] = $respuesta_dto;
            $respuestas_por_pregunta[$id_pregunta]['puntaje_total'] += (float) $respuesta->puntaje;
        }

        // Crear el array de RespuestaExamen
        $respuestas_examen = [];
        foreach ($respuestas_por_pregunta as $id_pregunta => $data) {
            $respuestas_examen[] = new RespuestaExamen(
                $id_pregunta,
                $data['respuestas'],
                number_format($data['puntaje_total'], 4, '.', '')
            );
        }

        return [
            'estudiante_examen' => $estudiante_examen,
            'respuestas_examen' => $respuestas_examen,
        ];
    }

    public function recalcularRespuestas($examen)
    {
        // Agrupar respuestas por pregunta
        $respuestas_por_pregunta = [];
        foreach ($examen->estudianteExamenRespuesta as $respuesta) {
            $id_pregunta = $respuesta->id_recurso_examen_pregunta;

            // Obtener el puntaje total de la pregunta
            $puntaje_pregunta = $respuesta->recursoExamenPregunta->puntaje;

            // Inicializar el array si no existe
            if (!isset($respuestas_por_pregunta[$id_pregunta])) {
                $respuestas_por_pregunta[$id_pregunta] = [
                    'respuestas' => [],
                    'puntaje_total' => 0.00,
                ];
            }

            // Manejar respuestas con y sin alternativas
            if ($respuesta->id_recurso_pregunta_alternativa && $respuesta->recursoPreguntaAlternativa) {
                // Respuesta con alternativa: calcular el puntaje basado en el porcentaje
                $porcentaje_alternativa = $respuesta->recursoPreguntaAlternativa->porcentaje;
                $puntaje_obtenido = ($porcentaje_alternativa / 100) * $puntaje_pregunta;
            } else {
                // Respuesta sin alternativa (pregunta tipo texto): usar el puntaje directamente
                $puntaje_obtenido = (float) $respuesta->puntaje;

                // Verificar si el puntaje calificado excede el nuevo peso de la pregunta
                if ($puntaje_obtenido > $puntaje_pregunta) {
                    $puntaje_obtenido = $puntaje_pregunta; // Ajustar al máximo permitido
                }
            }

            // Actualizar el campo `puntaje` en la tabla `estudiantes_examenes_respuestas`
            $respuesta->puntaje = number_format($puntaje_obtenido, 4, '.', '');
            $respuesta->save();

            // Crear el DTO Respuesta
            $respuesta_dto = new Respuesta(
                $respuesta->id_recurso_pregunta_alternativa ?? 0, // Usar 0 si no hay alternativa
                $respuesta->puntaje, // Usar el puntaje actualizado
                $respuesta->calificado
            );

            // Agregar la respuesta al array de respuestas de la pregunta
            $respuestas_por_pregunta[$id_pregunta]['respuestas'][] = $respuesta_dto;

            // Sumar el puntaje obtenido al puntaje total de la pregunta
            $respuestas_por_pregunta[$id_pregunta]['puntaje_total'] += $puntaje_obtenido;
        }

        // Crear el array de RespuestaExamen
        $respuestas_examen = [];
        foreach ($respuestas_por_pregunta as $id_pregunta => $data) {
            $respuestas_examen[] = new RespuestaExamen(
                $id_pregunta,
                $data['respuestas'],
                number_format($data['puntaje_total'], 4, '.', '')
            );
        }

        return [
            'estudiante_examen' => $examen,
            'respuestas_examen' => $respuestas_examen,
        ];
    }

    public static function calcularPuntajeTotal($respuestas_guardadas, $validacion_examen_enviado)
    {
        $fecha_actual = Carbon::now();
        $puntaje_total_examen = 0;
        $hay_pregunta_no_calificada = false;

        foreach ($respuestas_guardadas as $respuesta_guardada) {
            $tiene_puntaje_cero = collect($respuesta_guardada->respuestas)
                ->contains(function ($respuesta) {
                    return $respuesta->puntaje == '0.00';
                });
            
            $tiene_respuesta_no_calificada = collect($respuesta_guardada->respuestas)
                ->contains(function ($respuesta) {
                    return $respuesta->calificado === 'N';
                });

            if ($tiene_puntaje_cero) {
                $respuesta_guardada->puntaje_pregunta = '0.00';
            }

            if ($tiene_respuesta_no_calificada) {
                $hay_pregunta_no_calificada = true;
            }

            $puntaje_total_examen += (float) $respuesta_guardada->puntaje_pregunta;
        }

        $validacion_examen_enviado->puntaje = number_format($puntaje_total_examen, 2, '.', '');

        if (!$hay_pregunta_no_calificada) {
            $validacion_examen_enviado->estado_examen = 'CALIFICADO';
            $validacion_examen_enviado->fecha_calificacion = $fecha_actual;
        }

        $validacion_examen_enviado->save();

        return $validacion_examen_enviado;
    }

    public function recalificarExamenes($id_recurso_examen)
    {
        // Obtener los exámenes de estudiantes activos con sus respuestas y relaciones
        $estudiantes_examenes = EstudianteExamen::getPreguntasRespuestasByRecursoExamen($id_recurso_examen);

        // Si no hay exámenes para recalificar, retornar un mensaje
        if ($estudiantes_examenes->isEmpty()) {
            return [
                'examenes_recalificados' => [],
            ];
        }

        // Recalificar los exámenes de los estudiantes
        $examenes_recalificados = [];

        foreach ($estudiantes_examenes as $examen) {
            // Procesar las respuestas del examen
            $estudiante_examen = $this->recalcularRespuestas($examen);

            // Calcular el puntaje total del examen
            $calcular_puntaje_total = $this->calcularPuntajeTotal(
                $estudiante_examen['respuestas_examen'],
                $examen,
            );

            // Agregar el examen recalificado al array de resultados
            $examenes_recalificados[] = $calcular_puntaje_total;
        }

        return [
            'examenes_recalificados' => $examenes_recalificados,
        ];
    }
}
