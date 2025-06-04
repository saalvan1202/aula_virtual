<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModuloCompetencia extends Model
{
    protected $table='modulos_competencias';
    protected $fillable=[
        'id_componente','codigo','id_padre','descripcion','id_plan_estudio'
    ];
    public function scopeSelectAutocomplete($query)
    {
        return $query->selectRaw("
            modulos_competencias.id as id,
            modulos_competencias.id_padre,
            'MODULO'||p.codigo||'. '||modulos_competencias.codigo||' '||modulos_competencias.descripcion as competencia
        ")
            ->join('modulos_competencias as p','p.id','=','modulos_competencias.id_padre');
    }
    public function autocomplete($idPlan,$texto)
    {
        $sql="select
            mc.id,
            mc.id_padre,
            'MODULO'||p.codigo||'. '||mc.codigo||' '||mc.descripcion as competencia
            from
            modulos_competencias mc
            inner join modulos_competencias p on p.id=mc.id_padre
            where mc.estado='A'
            and p.id_plan_estudio=?
            and (mc.descripcion ilike ? or mc.codigo ilike ?)
            limit 10";
        return DB::select($sql,[$idPlan,$texto,$texto]);
    }
    public function getModulosParaCertificado($idEstudiante,$idPlan)
    {
        $sql="select
            m.id,
            m.codigo,
            m.descripcion,
            count(c.id) as total_cursos,
            count(cm.id_curso_docente) as total_aprobados,
            'CONCLUIDO' as estado
            from modulos_competencias m
            inner join modulos_competencias mc on mc.id_padre=m.id
            inner join cursos c on (c.id_modulo_competencia=mc.id and c.estado='A')
            left join cursos_docentes cd on (cd.id_cursos=c.id and cd.estado='A')
            left join cursos_matriculas cm on (cm.id_curso_docente=cd.id and cm.estado='A' and cm.estado_nota='APROBADO')
            left join matriculas mm on (mm.id=cm.id_matricula and mm.estado='A' and mm.id_estudiante=?)
            where m.id_plan_estudio=?
            and m.id_padre=0
            group by m.id";
        return DB::select($sql,[$idEstudiante,$idPlan]);
    }
    public function getCompetenciasIndicadores($idModulo)
    {
        $sql="select
            mc.id,
            mc.codigo,
            mc.descripcion,
            count(ic.id_modulo_competencia) as cantidad_indicadores,
            array_to_json(array_agg(ic.* order by ic.numero)) as indicadores
            from
            modulos_competencias mc
            left join indicadores_competencias ic on(ic.id_modulo_competencia=mc.id and ic.estado='A')
            where mc.id_padre=? and mc.estado='A'
            group by mc.id";
        return DB::select($sql,[$idModulo]);
    }
}
