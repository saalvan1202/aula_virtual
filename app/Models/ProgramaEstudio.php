<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramaEstudio extends Model
{
    protected $table='programa_estudios';
    protected $fillable=[
        'descripcion','codigo','nivel_formativo',
        'actividad_economica','duracion'
    ];
    public function sedes()
    {
        return $this->hasMany(ProgramaSede::class,'id_programa_estudio');
    }
    public function bolsaProgramaEstudio()
    {
        return $this->hasMany(BolsaProgramaEstudio::class,'id_programa_estudio');
    }
    public function planEstudio()
    {
        return $this->hasMany(PlanEstudio::class,'id_programa_estudio');
    }
    public function scopeWithSedes($query)
    {
        return $query->with(['sedes'=>function($q){
            $q->selectRaw('programas_sedes.id_sede,sedes.descripcion,id_programa_estudio')
                ->join('sedes','sedes.id','=','programas_sedes.id_sede')
                ->where('programas_sedes.estado','A');
        }]);
    }
}
