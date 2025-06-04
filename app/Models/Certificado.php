<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    protected $table = 'certificados';
    protected $fillable = [
        'id_estudiante', 'id_referencia','fecha_inicio','fecha_fin',
        'numero_registro_institucional','director_general','numero_registro_minedu'
    ];
    public function planEstudio()
    {
        return $this->belongsTo(PlanEstudio::class, 'id_plan_estudio');
    }
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante');
    }
    public function modulo()
    {
        return $this->belongsTo(ModuloCompetencia::class, 'id_referencia');
    }
    public function scopeWithEstudiante($query)
    {
        $query->with(['estudiante'=>function($q){
            $q->autocomplete();
        }]);
    }
    public function scopeWithModulo($query)
    {
        $query->with(['modulo'=>function($q){
            $q->join('modulos_competencias as mc','mc.id_padre','=','modulos_competencias.id');
            $q->join('cursos as c','c.id_modulo_competencia','=','mc.id');
            $q->selectRaw('
                modulos_competencias.id,modulos_competencias.codigo,modulos_competencias.descripcion,
                sum(c.creditos_teoria+c.creditos_practica) as total_creditos,
                sum(c.horas_teoria+c.horas_practica) as total_horas
            ');
            $q->groupBy('modulos_competencias.id');
        }]);
    }
    public  function scopeWithPlanEstudio($query)
    {
        $query->with(['planEstudio'=>function($q){
            $q->withProgramaEstudio();
        }]);
    }
}
