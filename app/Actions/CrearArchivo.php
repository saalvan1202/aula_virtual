<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Archivo;
use App\Models\RecursoTareaEstudiante;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class CrearArchivo
{
    protected $file;
    protected $pathToFile;
    protected $fileName;
    protected $mediaName;
    protected $code=null;
    protected $idKeys=[];
    protected $subject = null;
    protected $pathStorage=null;

    public function __construct()
    {

    }
    public function setSubject(Model $subject): self
    {
        $this->subject = $subject;
        $this->pathStorage=Archivo::getBasePath().'/'.$this->subject->getTable();
        return $this;
    }
    public function handle($file,$id=0)
    {
        if($file==null){
            return;
        }
        return $this->setFile($file)->create();
    }
    public function setIdKeys($idKeys)
    {
        $this->idKeys=$idKeys;
        return $this;
    }
    public function setCode($code)
    {
        $this->code=$code;
        return $this;
    }
    public function collection($files, $codes=null)
    {
        $result=[];
        foreach($files as $key=>$file)
        {
            if($codes){
                $this->code = $codes[$key];
            }
            $id=$this->idKeys[$key]??0;
            $result[]=$this->setFile($file)->create($id);
        }
        return $result;
    }
    public function collectionManyFiles($files, $codes = null)
    {
        $result = [];
        foreach ($files as $key => $file) {
            // Establecer un código único si se proporciona
            if ($codes) {
                $this->code = $codes[$key];
            }
            // Procesar y guardar cada archivo
            $result[] = $this->setFile($file)->create($key);
        }
        return $result;
    }
    public function setFile($file)
    {
        $this->file=$file;
        $this->pathToFile = $file->getPath().'/'.$file->getFilename();
        $this->fileName= md5($file->getRealPath()) . '.' . strtolower($file->getClientOriginalExtension());
        $this->mediaName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        return $this;
    }
    public function create($key=0)
    {
        try{
            $addSuccess=$this->attach();
            if($addSuccess){
                $referencia=$this->subject->getTable();
                $idReferencia=$this->subject->{$this->subject->getKeyName()};
                $archivo=Archivo::where('id_referencia',$idReferencia)
                    ->where('referencia',$referencia)
                    ->where('id_detalle',$key)
                    ->when(!is_null($this->code),function($q){
                        $q->where('codigo',$this->code);
                    })
                    ->first();
                if($archivo){
                    $archivo->unlink();
                }
                if(is_null($archivo)){
                    $archivo=new Archivo();
                    $archivo->uuid=(string)Str::uuid();
                }

                $archivo->nombre=$this->mediaName;
                $archivo->archivo=$this->fileName;
                $archivo->bytes=filesize($this->pathToFile);
                $archivo->id_detalle=$key;
                $archivo->codigo=$this->code;
                $archivo->referencia= $referencia;
                $archivo->id_referencia=$idReferencia;
                $archivo->estado='A';
                $archivo->save();

            }
        }catch(Exception $e){
            @unlink($this->pathStorage.'/',$this->fileName);
            logger('info',[
                'message'=>$e->getMessage(),
                'file'=>$e->getFile(),
                'code'=>$e->getCode(),
            ]);
            throw ValidationException::withMessages([
                'descripcion' => ['ocurrio un error al guardar el archivo'],
            ]);
        }

        return $archivo;
    }
    public function attach()
    {
        try{
            Storage::makeDirectory($this->pathStorage);
            Storage::putFileAs($this->pathStorage.'/',$this->file, $this->fileName);
        }catch(Exception $e){
            return false;
        }
        return true;
    }
}
