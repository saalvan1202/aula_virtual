<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecursoPreguntaAlternativa extends Model
{
    protected $table='recursos_preguntas_alternativas';
    protected $fillable=[
        'descripcion','respuesta','porcentaje','correcta','distractor'
    ];
    public function pregunta()
    {
        return $this->belongsTo(RecursoExamenPregunta::class,'id_recurso_examen_pregunta');
    }
    public function estudianteExamenRespuesta()
    {
        return $this->hasMany(EstudianteExamenRespuesta::class,'id_recurso_pregunta_alternativa');
    }
}
