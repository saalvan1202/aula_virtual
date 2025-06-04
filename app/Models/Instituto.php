<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Instituto extends Model
{
    protected $table='institutos';
    protected $fillable=[
        'denominacion','codigo','direccion','telefono','id_tipo_gestion_educativa',
        'email','director_general','id_ubigeo'
    ];
    public function ubigeo()
    {
        return $this->belongsTo(Ubigeo::class,'id_ubigeo');
    }

    public function tipoGestionEducativa()
    {
        return $this->belongsTo(TipoGestionEducativa::class,'id_tipo_gestion_educativa');
    }
    public function imagenes()
    {
        return $this->hasMany(Archivo::class,'id_referencia')
            ->where('referencia',static::getTable())
            ->whereIn('codigo',['logo','banner'])->orderBy('codigo');
    }
    public function getLogoAttribute()
    {
        $logo=$this->imagenes()->where('codigo','logo')->first();
        if($logo){
            return $logo->base64;
        }
        return '';
    }
    public function getBannerAttribute()
    {
        $banner=$this->imagenes()->where('codigo','banner')->first();
        if($banner){
            return $banner->base64;
        }
        return '';
    }
    public function getEmailNotification()
    {
        if(!empty($this->email)){
            return $this->email;
        }
        return config('mail.from.address');
    }
}
