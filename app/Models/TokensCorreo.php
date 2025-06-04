<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokensCorreo extends Model
{
    protected $table='tokens_correo';
    protected $fillable=[
        'token','usuario_id'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
