<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ambiente extends Model
{
    protected $table='aulas';
    protected $fillable=['descripcion','id_sede','id_tipo_aula','piso','ubicacion','aforo','estado'];

    public function horario() {
        return $this->hasMany(Horario::class, 'id_aula');
    }

    public function tipoAmbiente() {
        return $this->belongsTo(TipoAmbiente::class, 'id_tipo_aula');
    }
}
