<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Turno extends Model
{
    protected $table='turnos';
    protected $fillable=['descripcion','estado'];
}
