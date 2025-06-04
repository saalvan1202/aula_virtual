<?php

namespace App\Traits;

use App\Models\RecursoExamen;
use App\Models\EstudianteExamen;
use App\Models\EstudianteExamenRespuesta;
use Carbon\Carbon;

trait ValidatesExam
{
    public function validarExamen($id_recurso_examen)
    {
        $fecha_actual = Carbon::now();

        $recurso_examen = RecursoExamen::with('seccionRecurso')->find($id_recurso_examen);

        if (!$recurso_examen) {
            return response()->json(['error' => 'El examen no existe.'], 400);
        }

        if ($fecha_actual->lt($recurso_examen->fecha_inicio)) {
            return response()->json(['error' => 'El examen aún no está disponible.'], 400);
        }

        if ($fecha_actual->gt($recurso_examen->fecha_fin)) {
            return response()->json(['error' => 'El examen ha caducado.'], 400);
        }

        if ($recurso_examen->seccionRecurso->mostrar !== 'A') {
            return response()->json(['error' => 'El examen debe estar habilitado.'], 400);
        }

        return $recurso_examen;
    }

    public function validarExamenEnviado(int $id_curso_matricula, int $id_recurso_examen, bool $create_if_not_exists = false, string $fecha_inicio = "")
    {
        $fecha_actual = Carbon::now();

        $examen_enviado = EstudianteExamen::where([
            'id_curso_matricula' => $id_curso_matricula,
            'id_recurso_examen' => $id_recurso_examen,
            'estado' => 'A',
        ])->first();

        if ($examen_enviado) {
            if ($examen_enviado->estado_examen === 'CALIFICADO') {
                return response()->json(['error' => 'Su examen ya fue calificado.'], 400);
            }

            if (EstudianteExamenRespuesta::where('id_estudiante_examen', $examen_enviado->id)->exists()) {
                return response()->json(['error' => 'Ya tiene respuestas registradas.'], 400);
            }
        }

        // Si no existe y se permite crearlo
        if (!$examen_enviado && $create_if_not_exists) {

            $fields = [
                'id_curso_matricula' => $id_curso_matricula,
                'id_recurso_examen' => $id_recurso_examen,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_actual,
                'ip' => request()->ip(),
                'puntaje' => '0.00',
                'estado_examen' => 'PENDIENTE',
                'estado' => 'A',
            ];

            if (!empty($fecha_inicio)) {
                $fecha_inicio_carbon = Carbon::createFromFormat('Y-m-d H:i:s', $fecha_inicio);
        
                // Calcular la diferencia entre fecha_actual y fecha_inicio
                $diferencia = $fecha_actual->diff($fecha_inicio_carbon);
        
                // Formatear la diferencia en formato HH:MM:SS
                $diferencia_formateada = sprintf(
                    '%02d:%02d:%02d',
                    $diferencia->h, // Horas
                    $diferencia->i, // Minutos
                    $diferencia->s  // Segundos
                );
    
                // Agregar la diferencia formateada al array fields
                $fields['duracion'] = $diferencia_formateada;
            }        

            $examen_enviado = EstudianteExamen::create($fields);
        }

        return $examen_enviado;
    }
}