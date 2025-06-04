<?php

namespace App\Dtos;

class Respuesta
{
    public $id_answer;
    public $puntaje;
    public $calificado;

    public function __construct($id_answer, $puntaje, $calificado)
    {
        $this->id_answer = $id_answer;
        $this->puntaje = $puntaje;
        $this->calificado = $calificado;
    }
}