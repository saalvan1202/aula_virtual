<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TipoAmbiente extends Model
{
    protected $table='tipo_aulas';
    protected $fillable=['descripcion','estado'];

    public function ambiente(){
        return $this->hasMany(Ambiente::class, 'id_tipo_aula');
    }
}
