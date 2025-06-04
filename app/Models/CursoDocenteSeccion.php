<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CursoDocenteSeccion extends Model
{
    protected $table='cursos_docentes_secciones';
    protected $fillable=['nombre','descripcion','estado'];

    public function cursoDocente()
    {
        return $this->belongsTo(CursoDocente::class,'id_curso_docente');
    }

    public function seccionRecurso()
    {
        return $this->hasMany(SeccionRecurso::class,'id_curso_docente_seccion');
    }

}
