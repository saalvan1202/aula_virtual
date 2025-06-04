<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModalidadAdmision extends Model
{
    protected $table='modalidades_admision';
    protected $fillable=['descripcion','tipo'];
}
