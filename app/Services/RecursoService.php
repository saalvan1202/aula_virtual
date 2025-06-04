<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class RecursoService
{
    public function procesarRecurso($recurso, $fechaActual)
    {
        if ($fechaActual->gt(Carbon::parse($recurso->fecha_fin))) {
            $recurso->caducado = true;
            $recurso->no_disponible = false;
        } elseif ($fechaActual->lt(Carbon::parse($recurso->fecha_inicio))) {
            $recurso->caducado = false;
            $recurso->no_disponible = true;
        } else {
            $recurso->caducado = false;
            $recurso->no_disponible = false;
        }
    }

    public function procesarRecursos($recursos, $fechaActual)
    {
        if ($recursos instanceof Collection) {
            $recursos->map(function ($recurso) use ($fechaActual) {
                $this->procesarRecurso($recurso, $fechaActual);
            });
        } elseif ($recursos) {
            $this->procesarRecurso($recursos, $fechaActual);
        }
    }
}