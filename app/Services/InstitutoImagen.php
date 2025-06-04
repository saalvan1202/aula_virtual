<?php

namespace App\Services;

use App\Models\Archivo;
use App\Models\Instituto;
use Illuminate\Support\Facades\Cache;

class InstitutoImagen
{

    public function getArray()
    {
        set_time_limit(0);
        try{
            $key=schema_name().'_imagenes';
            if (Cache::has($key)) {
                return Cache::get($key);
            }
            $logo=url('images/logo.png');
            $banner=url('images/img-login.svg');
            $archivos=Archivo::where('referencia','institutos')->whereIn('codigo',['logo','banner'])->orderBy('id_referencia')->take(2)->get();
            foreach ($archivos as $archivo) {
                if($archivo->codigo=='logo' && file_exists($archivo->path)){
                    $logo=$archivo->url;
                }
                if($archivo->codigo=='banner' && file_exists($archivo->path)){
                    $banner=$archivo->url;
                }
            }
            $imagenes=[
                'logo'=>$logo,
                'banner'=>$banner,
            ];
            Cache::put($key, $imagenes, now()->addHours(8));
            return $imagenes;
        }catch (\Exception $e){
            abort(404);
        }
    }
    public function __getArray()
    {
        set_time_limit(0);
        if(session()->has('instituto_imagenes')){
            return session('instituto_imagenes');
        }
        try{
            $logo=url('images/logo.png');
            $banner=url('images/img-login.svg');
            $instituto=Instituto::with(['imagenes'=>function($q){
                $q->select('id','id_referencia','referencia','archivo','codigo');
            }])->select('id','denominacion','codigo')->where('estado','A')->first();
            if($instituto){
                if($instituto->logo!=''){
                    $logo=$instituto->logo;
                }
                if($instituto->banner!=''){
                    $banner=$instituto->banner;
                }
            }
            $imagenes=[
                'logo'=>$logo,
                'banner'=>$banner,
            ];
            session()->put('instituto_imagenes',$imagenes);
            return $imagenes;
        }catch (\Exception $e){
            abort(404);
        }

    }
}
