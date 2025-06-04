<?php

namespace App\Actions;

use App\Models\Instituto;
use App\Models\Ubigeo;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GuardarInstituto
{
    protected $crearArchivo;
    public function __construct(CrearArchivo $crearArchivo)
    {
        $this->crearArchivo = $crearArchivo;
    }
    public function handle(Request $request)
    {
        $obj=Instituto::find($request->input('id'));
        $ubigeo=Ubigeo::getNombreUbigeo($request->input('id_ubigeo'));
        if(is_null($obj)){
            $count=Instituto::where('estado','A')->count();
            if($count>1){
                throw ValidationException::withMessages([
                    'denominacion' => ['solo se puede crear un instituto'],
                ]);
            }
            $obj=new Instituto();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->departamento=$ubigeo->departamento;
        $obj->provincia=$ubigeo->provincia;
        $obj->distrito=$ubigeo->distrito;
        $obj->save();
        $this->storeImage($obj,$request);
        return $obj;
    }
    public function storeImage(Instituto $instituto, Request $request)
    {
        if($request->hasFile('logo')){
            $this->crearArchivo->setSubject($instituto)
                ->setCode('logo')
                ->handle($request->file('logo'));
        }
        if($request->hasFile('banner')){
            $this->crearArchivo->setSubject($instituto)
                ->setCode('banner')
                ->handle($request->file('banner'));
        }
    }
}
