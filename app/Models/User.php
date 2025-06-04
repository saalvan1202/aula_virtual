<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
/**
 * @method static \Illuminate\Database\Eloquent\Builder joinPersona()
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table='usuarios';
    protected $attributes=[
        'id_referencia'=>0,
        'referencia'=>'usuarios',
        'es_usuario'=>'S'
    ];

    protected $fillable = [
        'id','id_perfil', 'email',
        'usuario','id_tipo_contrato','email','telefono','profesion', 'registro_completado'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function perfil()
    {
        return $this->belongsTo(Perfil::class,'id_perfil');
    }
    public function persona()
    {
        return $this->belongsTo(Persona::class,'id_persona');
    }
    public function estudiante()
    {
        return $this->hasOne(Estudiante::class,'id_usuario');
    }
    public function cursoDocente()
    {
        return $this->hasMany(CursoDocente::class,'id_docente');
    }
    public function tokensCorreo()
    {
        return $this->hasMany(TokensCorreo::class, 'usuario_id');
    }
    public function scopeJoinPersona($query)
    {
        return $query->join('personas','personas.id','=','usuarios.id_persona')
            ->selectRaw('usuarios.*,
                personas.id_tipo_documento_identidad,
                personas.nombres,
                personas.apellido_paterno,
                personas.apellido_materno,
                personas.numero_documento,
                personas.numero_documento,
                personas.sexo,
                personas.estado_civil,
                personas.fecha_nacimiento
            ');
    }
}
