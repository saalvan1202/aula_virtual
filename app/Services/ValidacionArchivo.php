<?php

namespace App\Services;

class ValidacionArchivo
{
    protected $error=null;
    protected $extensions=[];
    protected $maxSize=1024 * 1024 * 8;//8MB
    public function setExtension($extensions)
    {
        $this->extensions=$extensions;
    }
    public function setMaxSize($maxSize): ValidacionArchivo
    {
        $this->maxSize=$maxSize;
        return $this;
    }
    public function validate($file): ValidacionArchivo
    {
        $this->error=null;
        if(is_null($file)){
            $this->error='No se selecciono un archivo';
            return $this;
        }
        if($file->getPath() === ''){
            $this->error='No se selecciono un archivo';
            return $this;
        }
        if (!$file->isValid()) {
            $this->error='error de archivo';
            return $this;
        }
        if($file->getSize()<1){
            $this->error='El archivo es incorrecto';
            return $this;
        }
        if (!in_array(strtolower($file->getClientOriginalExtension()), $this->extensions)) {
            $these = implode(', ', $this->extensions);
            $this->error='El archivo '.$file->getClientOriginalName().' debe ser un archivo de tipo ['.$these.']';
            return $this;
        }
        $pathToFile = $file->getPath().'/'.$file->getFilename();

        if (filesize($pathToFile) > $this->maxSize) {

            $this->error='El archivo '.$file->getClientOriginalName().' tiene un tamaño de '.human_readable_size(filesize($pathToFile)).' que es mayor que el máximo permitido '.human_readable_size($this->maxSize);
            return $this;
        }
        return $this;
    }
    public function getError()
    {
        return $this->error;
    }
}
