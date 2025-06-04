<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Apoderado extends Model
{
    protected $table='apoderados';
    protected $fillable=[
        'direccion','telefono','email'
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class,'id_persona');
    }
    public function scopeJoinPersona($query)
    {
        return $query->join('personas','personas.id','=','apoderados.id_persona')
            ->selectRaw("apoderados.*,
                personas.id_tipo_documento_identidad,
                personas.nombres,
                personas.apellido_paterno,
                personas.apellido_materno,
                personas.numero_documento,
                personas.sexo,
                personas.estado_civil,
                personas.fecha_nacimiento,
            ");
    }
    public function autocomplete($texto)
    {
        $sql="
            select
            a.id,
            p.numero_documento||' '||p.apellido_paterno||' '||p.apellido_materno ||' '||p.nombres as label,
            p.apellido_paterno||' '||p.apellido_materno ||' '||p.nombres as apoderado,
            p.numero_documento,
            tdi.abreviatura as tipo_documento
            from
            apoderados as a
            inner join personas as p on p.id=a.id_persona
            inner join tipo_documentos_identidades tdi on tdi.id=p.id_tipo_documento_identidad
            where a.estado='A'
            and (p.numero_documento ilike ? or p.nombres ilike ? or p.apellido_paterno ilike ? or p.apellido_materno ilike ?)
            limit 10";
        return DB::select($sql,[$texto,$texto,$texto,$texto]);
    }
}
