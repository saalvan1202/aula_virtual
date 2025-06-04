<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoRecurso extends Model
{
    protected $table='tipos_recursos';
    protected $fillable=['nombre','descripcion','slug','icono'];

    public function seccionRecurso()
    {
        return $this->hasMany(SeccionRecurso::class,'id_tipo_recurso');
    }

}
