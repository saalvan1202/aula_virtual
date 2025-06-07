<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokensCorreoCliente extends Model
{
    protected $table='tokens_correo_clientes';
    protected $fillable=[
        'token'
    ];
}
