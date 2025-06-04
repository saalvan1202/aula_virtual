<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Componente extends Model
{
    protected $table='componentes';
    protected $fillable=['descripcion'];
}
