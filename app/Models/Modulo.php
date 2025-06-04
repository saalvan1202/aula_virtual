<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Modulo extends Model
{
    protected $table='modulos';
    protected $fillable=['titulo','id_padre','ruta','icono','orden'];
    public function getTree($idPerfil)
    {
        return Modulo::selectRaw("modulos.id,titulo,id_padre,coalesce(p.estado,'I') as estado ")
            ->leftJoin('permisos as p',function($join) use ($idPerfil){
                $join->on('p.id_modulo','=','modulos.id');
                $join->on('p.id_perfil','=',DB::raw($idPerfil));
            })->where('modulos.estado','A')
            ->orderBy('id_padre')
            ->orderBy('orden')
            ->orderBy('titulo')
            ->get()
            ->toArray();
    }
    public static function  getPermisos($idPerfil)
    {
        return Modulo::selectRaw('
                modulos.id,modulos.titulo as title,modulos.ruta as route,modulos.ruta,
                modulos.icono as icon,
                modulos.id_padre
            ')
            ->join('permisos as p',function($join){
                $join->on('p.id_modulo','=','modulos.id');
            })
            ->where('modulos.estado','A')
            ->where('p.estado','A')
            ->where('p.id_perfil',$idPerfil)
            ->orderBy('id_padre')
            ->orderBy('orden')
            ->orderBy('titulo')
            ->get()
            ->toArray();
    }
}
