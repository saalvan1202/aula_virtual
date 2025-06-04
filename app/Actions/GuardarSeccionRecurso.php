<?php

namespace App\Actions;

use App\Models\SeccionRecurso;
use Illuminate\Database\Eloquent\Model;

class GuardarSeccionRecurso
{
    public function handle($idCursoSeccion,Model $subject)
    {
        $obj=new SeccionRecurso();
        $obj->id_curso_docente_seccion=$idCursoSeccion;
        $obj->referencia=$subject->getTable();
        $obj->id_referencia=$subject->{$subject->getKeyName()};
        $obj->completado='N';
        $obj->estado='A';
        $obj->save();
        return $obj;
    }
}
