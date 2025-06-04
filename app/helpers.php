<?php
if(!function_exists('utf8_to_decode')){
    function utf8_to_decode($string){
        return mb_convert_encoding($string,'ISO-8859-1', 'UTF-8');
    }
}
if(!function_exists('utf8_to_encode')){
    function utf8_to_encode($string){
        return mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
    }
}
if(!function_exists('fecha_largo')){
    function fecha_largo($date){
        if($date==null){
            return $date;
        }
        list($year,$month,$day) = explode("-",$date);
        return $day.' de '.mes_nombre($month).' del '.$year;
    }
}
if(!function_exists('mes_nombre')){
    function mes_nombre($month)
    {
        $months = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        return $months[$month-1]??'';
    }
}
if (! function_exists('human_readable_size')) {
    function human_readable_size($sizeInBytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        if ($sizeInBytes === 0.0) {
            return '0 '.$units[1];
        }
        for ($i = 0; $sizeInBytes > 1024; $i++) {
            $sizeInBytes /= 1024;
        }

        return round($sizeInBytes, 2).' '.$units[$i];
    }
}
if (! function_exists('schema_name')) {
    function schema_name()
    {

        return (new \App\Multitenancy\HostFinder())->findForRequest(request());
    }
}
if (! function_exists('schema_link')) {
    function schema_link()
    {
       return (new \App\Multitenancy\HostFinder())->findForConsole();
    }
}
if (! function_exists('is_multitenancy')) {
    function is_multitenancy()
    {

        return config('instituto.multitenancy');
    }
}
if (! function_exists('array_combo')) {
    function array_combo($select)
    {
        return collect($select)->map(function ($value, $key) {
            return (object) ['id' => $key, 'descripcion' => $value];
        })->values();
    }
}
if (! function_exists('menu_dependencia')) {
    function menu_dependencia($menu,$url=null)
    {
        if($url==null){
            $url=request()->getPathInfo();
        }
        return [
            [
                'menu'=>$menu,
                'url'=>ltrim($url,'/')
            ]
        ];
    }
}
