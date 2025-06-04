<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Empresa extends Model
{
    protected $table='empresas';
    protected $fillable=['ruc','razon_social','nombre_comercial','rubro','id_ubigeo', 'direccion','telefono','representante_legal','email', 'pagina_web','descripcion','estado'];

    public function ubigeo() {
        return $this->belongsTo(Ubigeo::class, 'id_ubigeo');
    }

    public function bolsaLaboral()
    {
        return $this->hasMany(BolsaLaboral::class, 'id_empresa');
    }

    public function autocomplete($texto)
    {
        $sql="select
                e.id, e.ruc, e.razon_social, e.nombre_comercial,
                e.rubro, e.id_ubigeo, e.direccion, e.telefono,
                e.representante_legal, e.email, e.pagina_web,
                e.descripcion, e.ruc || ' - ' || coalesce(e.razon_social, e.nombre_comercial) AS empresa
            from empresas as e
            where e.estado='A' and (e.ruc ilike ? or e.nombre_comercial ilike ? or e.razon_social ilike ?)";
        return DB::select($sql,[$texto,$texto,$texto]);
    }
}
