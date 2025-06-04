<?php

namespace App\Services;

use App\Models\Modulo;

class Menu
{
    public function menu($idPerfil=0)
    {

        if($idPerfil==0){
            return [];
        }
        if(session()->has('menus')){
            return session()->get('menus');
        }

        $records= Modulo::getPermisos($idPerfil);
        session()->put('menus',$records);
        return $records;
    }

    public function all()
    {

        $data=collect($this->menu(auth()->user()->id_perfil));
        return $this->getMenus($data);
    }
    public function getMenus($data)
    {

        $menus=new static();
        $menuAll = [];
        foreach ($data->where('id_padre',0)->values()->all() as $line) {
            $item = [ array_merge($line, ['children' => $menus->getChildren($data, $line) ]) ];
            $menuAll = array_merge($menuAll, $item);
        }
        return $menus->menuAll = $menuAll;
    }
    public function getChildren($data, $line)
    {
        $children = [];
        foreach ($data->where('id_padre',$line['id'])->values()->all() as $line1) {
            $children = array_merge($children, [ array_merge($line1, ['children' => $this->getChildren($data, $line1) ]) ]);
        }
        return $children;
    }
}
