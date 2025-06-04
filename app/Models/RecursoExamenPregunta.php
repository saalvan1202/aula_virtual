<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecursoExamenPregunta extends Model
{
    protected $table='recursos_examenes_preguntas';
    protected $fillable=[
        'id_tipo_pregunta','descripcion','duracion','puntaje',
        'retroalimentacion_buena','retroalimentacion_mala',
        'id_recurso_examen'
    ];
    public function tipoPregunta()
    {
        return $this->belongsTo(TipoPregunta::class,'id_tipo_pregunta');
    }
    public function examen()
    {
        return $this->belongsTo(RecursoExamen::class,'id_recurso_examen');
    }
    public function alternativas()
    {
        return $this->hasMany(RecursoPreguntaAlternativa::class,'id_recurso_examen_pregunta');
    }
    public function estudianteExamenRespuesta()
    {
        return $this->hasMany(EstudianteExamenRespuesta::class,'id_recurso_examen_pregunta');
    }

    public static function getSeccionRecurso($id_recurso_examen_pregunta){
        return static::selectRaw("sr.id,sr.mostrar,sr.estado,re.id as id_recurso_examen")
        ->join('recursos_examenes as re','re.id','=','recursos_examenes_preguntas.id_recurso_examen')
        ->join('secciones_recursos as sr','sr.id','=','re.id_seccion_recurso')
        ->where('recursos_examenes_preguntas.id',$id_recurso_examen_pregunta)
        ->first();
    }
}
