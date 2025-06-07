<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ContinuarRegistroCliente extends Mailable
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        $logo = asset('logo.png');  // ruta pública del logo

        $url = url('/continuar-registro/' . $this->token);
        return $this->view('mails.continuar-registro', [
            'url' => $url,
            'usuario' => 'Estimado cliente',
            'logo' => $logo,
            'app_name' => 'Aula Virtual'
        ])->subject('Continúa tu registro en PVirtual');
    }
}
