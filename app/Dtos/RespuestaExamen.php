<?php

namespace App\Dtos;

class RespuestaExamen
{
    public $id_pregunta;
    public $respuestas;
    public $puntaje_pregunta;

    public function __construct($id_pregunta, $respuestas, $puntaje_pregunta)
    {
        $this->id_pregunta = $id_pregunta;
        $this->respuestas = $respuestas;
        $this->puntaje_pregunta = $puntaje_pregunta;
    }
}