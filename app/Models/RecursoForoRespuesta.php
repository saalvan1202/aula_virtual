<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecursoForoRespuesta extends Model
{
    protected $table = 'recursos_foros_respuestas';
    protected $fillable = [
        'id_recurso_foro', 'id_curso_matricula', 'id_padre', 'descripcion',
        'fecha_calificacion','estado_calificacion','puntaje',
        'tipo','url'
    ];
    protected $attributes = [
        'estado_calificacion'=>'PENDIENTE',
        'tipo'=>'URL'
    ];

    public function recursoForo()
    {
        return $this->belongsTo(RecursoForo::class,'id_recurso_foro');
    }
    public function cursoMatricula()
    {
        return $this->belongsTo(CursoMatricula::class,'id_recurso_foro');
    }

}
