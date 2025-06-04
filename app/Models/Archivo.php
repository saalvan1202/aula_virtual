<?php

namespace App\Models;

use App\Services\Image;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use SplFileInfo;

class Archivo extends Model
{
    protected $table='archivos';
    protected $fillable=[
        'descripcion'
    ];

    protected $hidden=[
        'id_referencia',
        'id_detalle',
        'codigo'
    ];
    protected function serializeDate(\DateTimeInterface $date) : string
    {
        $carbonInstance = Carbon::instance($date);
        return $carbonInstance->toDateTimeString();
    }

    public function getPathAttribute()
    {
        return storage_path('app/'.static::getBasePath()) .'/'.$this->referencia . '/' . $this->archivo;
    }
    public function getUrlAttribute()
    {
        return url('archivos/mostrar/'.$this->id.'/'.$this->archivo);
    }
    public function getBase64Attribute()
    {
        if(file_exists($this->path)){
            $file=file_get_contents($this->path);
            return Image::encode64($file);
        }
        return '';

    }
    public function unlink()
    {
        @unlink($this->path);
    }
    public static function getBasePath(): string
    {
        $base='archivos';
        if(is_multitenancy()){
            $base.='/'.schema_name();
        }
        return $base;
    }
}
