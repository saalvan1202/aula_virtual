<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Seccion extends Model
{
    protected $table='secciones';
    protected $fillable=['descripcion','estado'];
}
