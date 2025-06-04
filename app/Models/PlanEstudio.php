<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanEstudio extends Model
{
    protected $table='plan_estudios';
    protected $fillable=[
        'descripcion','id_programa_estudio','modalidad'
    ];
    public function programaEstudio()
    {
        return $this->belongsTo(ProgramaEstudio::class,'id_programa_estudio');
    }
    public function modulos()
    {
        return $this->hasMany(ProgramaSede::class,'id_programa_estudio');
    }
    public function scopeWithProgramaEstudio($query)
    {
        return $query->with(['programaEstudio'=>function($q){
            return $q->selectRaw('id,descripcion,nivel_formativo');
        }]);
    }
}
