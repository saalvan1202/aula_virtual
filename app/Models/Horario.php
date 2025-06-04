<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Horario extends Model
{
    protected $table='horarios';
    protected $fillable=['id_curso_docente','id_aula', 'dia', 'tipo', 'hora_inicio', 'hora_fin', 'estado'];

    public function cursoDocente(){
        return $this->belongsTo(CursoDocente::class, 'id_curso_docente');
    }

    public function ambiente(){
        return $this->belongsTo(Ambiente::class, 'id_aula');
    }
}
