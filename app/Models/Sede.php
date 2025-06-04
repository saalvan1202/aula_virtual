<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sede extends Model
{
    protected $table='sedes';
    protected $fillable=['descripcion','id_instituto'];

    public function cursoDocente() {
        return $this->hasMany(CursoDocente::class, 'id_sede');
    }
}
