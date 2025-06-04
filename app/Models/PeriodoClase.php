<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PeriodoClase extends Model
{
    protected $table='periodo_clases';
    protected $fillable=['anio','descripcion','fecha_inicio','fecha_fin'];

    public function cursoDocente() {
        return $this->hasMany(CursoDocente::class, 'id_periodo_clases');
    }
    public function scopeAnioActual($query)
    {
        $query->where('anio',date('Y'));
    }
}
