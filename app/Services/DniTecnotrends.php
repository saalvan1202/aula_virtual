<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;

class DniTecnotrends
{
    protected $error=null;
    protected $data=[];
    public function get($dni)
    {

        try{
            $this->error='Ocurrio un problema conectando a api dni';
            $this->data=[];
            $http=Http::withHeaders([
                    'Referer' => 'pvirtual',
                ])->acceptJson()
                ->withOptions(["verify"=>false])
                ->withToken('tecnotrends')
                ->get('https://tecnotrends.pe/api_dni/index.php?dni='.$dni);
            if($http->ok()){
                $response = $http->json();
                if(isset($response['error'])){
                    $this->error=$response['error'];
                }else{
                    $this->error=null;
                    $this->data= [
                        'nombre_completo'=>$response['nombre'],
                        'direccion'=>$response['direccion'],
                        'id_tipo_documento'=>1,
                        'nombres'=>$response['nombres'],
                        'apellido_paterno'=>$response['apellidoPaterno'],
                        'apellido_materno'=>$response['apellidoMaterno'],
                        'apellidos'=>trim($response['apellidoPaterno'].' '.$response['apellidoMaterno'])
                    ];
                }

            }
        }catch (\Exception $e){
            $this->error='Ocurrio un error inesperado';
        }

    }
    public function getError()
    {
        return $this->error;
    }
    public function result()
    {
        return $this->data;
    }
}
