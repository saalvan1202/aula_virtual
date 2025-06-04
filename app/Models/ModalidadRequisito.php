<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModalidadRequisito extends Model
{
    protected $table='modalidad_requisitos';
    protected $fillable=[
        'id_modalidades_admision','descripcion','obligatorio','estado'
    ];
}
