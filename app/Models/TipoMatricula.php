<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TipoMatricula extends Model
{
    protected $table='tipo_matriculas';
    protected $fillable=['descripcion','estado'];
}
