<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Ubigeo extends Model
{
    protected $table='ubigeos';
    protected $fillable=[];
    protected $hidden=['created_at','updated_at'];

    public function empresa()
    {
        return $this->hasMany(Empresa::class, 'id_ubigeo');
    }

    public function bolsaLaboral()
    {
        return $this->hasMany(BolsaLaboral::class, 'id_ubigeo');
    }

    public function scopeSelectCombo($query)
    {
        $query->select('id','nombre','cod_dpto','cod_prov','cod_dist','cod_ccpp')
            ->where('estado','A')
            ->orderBy('nombre','ASC');
    }
    public function scopeSelectDepartamento($query)
    {
        $query->where('cod_prov','00')
            ->where('cod_dist','00')
            ->where('cod_ccpp','0000');
    }
    public function scopeSelectProvincia($query,$codDepartamento)
    {
        $query->where('cod_dpto',$codDepartamento)
            ->where('cod_prov','<>','00')
            ->where('cod_dist','00')
            ->where('cod_ccpp','0000');
    }
    public function scopeSelectDistrito($query,$codDepartamento,$codProvincia)
    {
        $query->where('cod_dpto',$codDepartamento)
            ->where('cod_prov',$codProvincia)
            ->where('cod_dist','<>','00')
            ->where('cod_ccpp','0000');
    }
    public static function getNombreUbigeo($id)
    {
        $sql="select
            d.id,
            d.cod_dpto,
            d.cod_prov,
            d.cod_dist,
            d.nombre as distrito,
            p.nombre as provincia,
            dd.nombre as departamento
            from
            ubigeos as d
            left join ubigeos p on (p.cod_dpto=d.cod_dpto and p.cod_prov=d.cod_prov and p.cod_dist='00')
            left join ubigeos dd on (dd.cod_dpto=d.cod_dpto and dd.cod_prov='00' and dd.cod_dist='00')
            where d.id=?";
        return DB::selectOne($sql,[$id]);
    }
    public function getFullUbigeo($id)
    {
        $sql = "SELECT
                d.id,
                d.cod_dpto,
                dd.nombre AS departamento,
                d.cod_prov,
                p.nombre AS provincia,
                d.cod_dist,
                d.nombre AS distrito
            FROM ubigeos AS d
            LEFT JOIN ubigeos p ON (p.cod_dpto = d.cod_dpto AND p.cod_prov = d.cod_prov AND p.cod_dist = '00')
            LEFT JOIN ubigeos dd ON (dd.cod_dpto = d.cod_dpto AND dd.cod_prov = '00' AND dd.cod_dist = '00')
            WHERE d.id = ?";
        return DB::selectOne($sql, [$id]);
    }
    public function getFullUbigeoForName($distrito)
    {
        $sql = "SELECT
                d.id,
                d.cod_dpto,
                dd.nombre AS departamento,
                d.cod_prov,
                p.nombre AS provincia,
                d.cod_dist,
                d.nombre AS distrito
            FROM ubigeos AS d
            LEFT JOIN ubigeos p ON (p.cod_dpto = d.cod_dpto AND p.cod_prov = d.cod_prov AND p.cod_dist = '00')
            LEFT JOIN ubigeos dd ON (dd.cod_dpto = d.cod_dpto AND dd.cod_prov = '00' AND dd.cod_dist = '00')
            WHERE d.nombre = ?";
        return DB::selectOne($sql, [$distrito]);
    }
    public function getFullUbigeoForCode($code)
    {
        $sql = "SELECT * FROM ubigeos AS u
            WHERE trim(u.cod_dpto||''||u.cod_prov||''||u.cod_dist) = ?
            and u.cod_ccpp = '0000'";
        return DB::selectOne($sql, [$code]);
    }
}
