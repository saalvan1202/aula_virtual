<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Dispositivo extends Model
{
    protected $table = 'dispositivos';

    protected $fillable = [
        'id_usuario',
        'ip',
        'so',
        'version_so',
        'dispositivo',
        'es_movil',
        'es_escritorio',
        'es_tablet',
        'estado'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
