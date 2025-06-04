<?php

namespace App\Http\Controllers;

use App\Services\Variable;
use Rap2hpoutre\LaravelLogViewer\LogViewerController as BaseLogViewerController;
class LogViewerController extends BaseLogViewerController
{
    public function index()
    {
        set_time_limit(0);
        $log=false;
        if(Variable::isMaster(auth()->user()->id)){
           $log=true;
        }
        if(is_multitenancy() && schema_name()!='tecnotrends'){
            $log=false;
        }
        if(Variable::isMaster(auth()->user()->id) && app()->environment()=='local'){
            $log=true;
        }
        if($log){
            return parent::index();
        }
        abort(403,'no autorizado');
    }
}
