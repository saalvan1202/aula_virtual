<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;
    protected $table='personas';
    protected $fillable=[
        'id_tipo_documento_identidad','nombres','apellido_paterno',
        'apellido_materno','numero_documento','fecha_nacimiento',
        'sexo','estado_civil',
    ];
    public function getApellidosCompletosAttribute()
    {
        return $this->apellido_paterno.' '.$this->apellido_materno.' '.$this->nombres;
    }
    public function tipoDocumentoIdentidad()
    {
        return $this->belongsTo(TipoDocumentoIdentidad::class,'id_tipo_documento_identidad');
    }
    public function usuarios()
    {
        return $this->hasMany(User::class,'id_persona');
    }
}
