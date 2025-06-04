<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Services\Image;
use SplFileInfo;

class ArchivoController extends Controller
{
    public function download($id,$archivo)
    {
        $obj=Archivo::where('id',$id)
            ->where('archivo',$archivo)->firstOrFail();
        if(is_null($obj)){
            abort(404);
        }
        if(!file_exists($obj->path)){
            abort(404);
        }

        $file = new SplFileInfo((string) $obj->path);
        $mimetype=mime_content_type($obj->path);
        $content=file_get_contents($file->getRealPath());
        ob_get_clean();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE, HEAD");
        header("Allow: GET, POST, OPTIONS, PUT, DELETE");
        header('Content-Disposition: inline; filename="'.$obj->nombre.'"');
        header('Content-Type:'.$mimetype ?: 'application/octet-stream');
        header('Content-Length: '.strlen( $content ));
        header('Cache-Control: public, must-revalidate, max-age=0');
        echo $content ;

    }
    public function preview($id,$archivo)
    {
        $obj=Archivo::where('id',$id)
            ->where('archivo',$archivo)->firstOrFail();

        if(is_null($obj)){
            abort(404);
        }
        if(!file_exists($obj->path)){
            abort(404);
        }
        $mimetype = mime_content_type($obj->path);
        // if($mimetype=='application/pdf'){
        //     abort(404,'no existe vista previa para archivos pdf');
        // }
        header("Content-Type: ".$mimetype);
        header("Content-Length: " . filesize($obj->path));
        header("Cache-Control: public, max-age=604800"); // 1 semana
        header("Expires: " . gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT');
        //header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        //header("Expires: " . gmdate("D, d M Y H:i:s", time()));

        readfile($obj->path);
        exit;
    }
}
