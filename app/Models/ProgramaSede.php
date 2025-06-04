<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramaSede extends Model
{
    protected $table='programas_sedes';
    protected $fillable=[
        'id_sede','estado'
    ];
}
