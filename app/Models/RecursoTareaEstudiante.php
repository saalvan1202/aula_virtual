<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecursoTareaEstudiante extends Model
{
    protected $table = 'recursos_tareas_estudiantes';
    protected $fillable = [
        'id_curso_matricula','fecha_calificacion','estado_calificacion','puntaje',
        'tipo','url','observaciones','estado_envio'
    ];
    protected $attributes=[
        'estado_calificacion'=> 'PENDIENTE',
        'estado_envio'=>'PENDIENTE'
    ];

    public function recursoTarea()
    {
        return $this->belongsTo(RecursoTarea::class,'id_recurso_tarea');
    }

    public function cursoMatricula()
    {
        return $this->belongsTo(CursoMatricula::class,'id_curso_matricula');
    }

    public function archivos()
    {
        return $this->hasOne(Archivo::class, 'id_referencia', 'id')
            ->where('referencia',static::getTable());
    }
}
