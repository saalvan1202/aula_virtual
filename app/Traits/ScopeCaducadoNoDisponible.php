<?php

namespace App\Traits;

trait ScopeCaducadoNoDisponible
{
    public function scopeCaducadoNoDisponible($query)
    {
        return $query->selectRaw('case when now()>fecha_fin then false
            when now()<fecha_inicio then true
            else false
            end as no_disponible,
            case when now()>fecha_fin then true
            when now()<fecha_inicio then false
            else false
            end as caducado');
    }
}
