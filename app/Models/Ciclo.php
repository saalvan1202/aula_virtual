<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ciclo extends Model
{
    protected $table='ciclos';
    protected $fillable=['descripcion','orden'];
}
