<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BolsaProgramaEstudio extends Model
{
    protected $table='bolsas_programa_estudios';
    protected $fillable=['id_bolsa_laboral','id_programa_estudio'];

    public function programaEstudio()
    {
        return $this->belongsTo(ProgramaEstudio::class, 'id_programa_estudio');
    }
    public function bolsaLaboral()
    {
        return $this->belongsTo(BolsaLaboral::class, 'id_bolsa_laboral');
    }
}
