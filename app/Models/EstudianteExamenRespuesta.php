<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstudianteExamenRespuesta extends Model
{
    protected $table = 'estudiantes_examenes_respuestas';
    protected $fillable = [
        'id_estudiante_examen', 'id_recurso_examen_pregunta', 'id_recurso_pregunta_alternativa', 'respuesta',
        'calificado','puntaje','estado'
    ];

    public function estudianteExamen()
    {
        return $this->belongsTo(EstudianteExamen::class,'id_estudiante_examen');
    }
    public function recursoExamenPregunta()
    {
        return $this->belongsTo(RecursoExamenPregunta::class,'id_recurso_examen_pregunta');
    }
    public function recursoPreguntaAlternativa()
    {
        return $this->belongsTo(RecursoPreguntaAlternativa::class,'id_recurso_pregunta_alternativa');
    }

}
