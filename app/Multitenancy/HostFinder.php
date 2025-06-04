<?php

namespace App\Multitenancy;

use Illuminate\Http\Request;

class HostFinder
{
    public function findForRequest(Request $request)
    {
        return str_replace(['/','index.php'],"",$request->getBaseUrl());
    }
    public function findForConsole()
    {
        $path_relative=getcwd();
        if(isset($_SERVER['PWD'])){
            $path_relative=$_SERVER['PWD'];
        }
        $path= str_replace(['/','-',"\\",' '],"@",$path_relative);
        $path=array_reverse(explode('@',$path));
        return $path[0];
    }
}
