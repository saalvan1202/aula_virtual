<?php


namespace App\Services;


use Illuminate\Support\Facades\Http;

class RucTecnotrends
{
    const URL_CONSULT = 'http://tecnotrends.pe/padron_sunat/index.php';
    private $error;
    protected $data=[];
    public function get($ruc)
    {
        $this->error=null;
        $this->data=[];
        if (strlen($ruc) !== 11) {
            $this->error = 'Ruc debe tener 11 dígitos';
            return false;
        }
        $response=$this->getApi(self::URL_CONSULT.'?ruc='.$ruc);
        if ($response === false) {
            return false;
        }
        $this->parseData($response);
    }
    private function getApi($url)
    {
        try{
            $this->error='Ocurrio un problema conectando a api ruc ';
            $http=Http::withHeaders([
                'Referer' => 'pvirtual',
            ])->acceptJson()
                ->withOptions(["verify"=>false])
                ->withToken('tecnotrends')
                ->get($url);
            if($http->ok()){
                $this->error=null;
                return $http->json();
            }
            return false;
        }catch (\Exception $e){
            $this->error='Ocurrio un error inesperado';
            return false;
        }

    }
    private function parseData($data)
    {
        if($data['success']===false){
            $this->error=$data['message'];
            return false;
        }
        $data=$data['data'];
        $this->data= [
           'razon_social'=>$data['nombre_o_razon_social'],
           'nombre_comercial'=>'',
           'direccion'=>$data['direccion'],
            'telefonos'=>''
        ];

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
