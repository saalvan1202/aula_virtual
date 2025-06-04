<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstudianteExamen extends Model
{
    protected $table = 'estudiantes_examenes';
    protected $fillable = [
        'id_recurso_examen', 'id_curso_matricula', 'fecha_inicio', 'fecha_fin',
        'duracion','puntaje','ip',
        'estado_examen','estado'
    ];

    public function recursoExamen()
    {
        return $this->belongsTo(RecursoExamen::class,'id_recurso_examen');
    }
    public function cursoMatricula()
    {
        return $this->belongsTo(CursoMatricula::class,'id_curso_matricula');
    }
    public function estudianteExamenRespuesta()
    {
        return $this->hasMany(EstudianteExamenRespuesta::class,'id_estudiante_examen');
    }

    public static function getPreguntasRespuestasByRecursoExamen($id_recurso_examen) {
        return static::where('id_recurso_examen', $id_recurso_examen)
        ->where('estado', 'A')
        ->with([
            'estudianteExamenRespuesta' => function ($query) {
                $query->where('estado', 'A')
                    ->select([
                        'id',
                        'id_estudiante_examen',
                        'id_recurso_examen_pregunta',
                        'id_recurso_pregunta_alternativa',
                        'calificado',
                        'puntaje'
                    ]);
            },
            'estudianteExamenRespuesta.recursoExamenPregunta' => function ($query) {
                $query->select(['id', 'puntaje']);
            },
            'estudianteExamenRespuesta.recursoPreguntaAlternativa' => function ($query) {
                $query->select(['id', 'porcentaje']);
            }
        ])
        ->get();
    }

}
