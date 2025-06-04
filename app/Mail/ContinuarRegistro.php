<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ContinuarRegistro extends Mailable
{
    public $user;
    public $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        $logo = asset('logo.png');  // ruta pública del logo

        $url = url('/continuar-registro/' . $this->token);
        return $this->view('mails.continuar-registro', [
            'url' => $url,
            'usuario' => $this->user->usuario,
            'logo' => $logo,
            'app_name' => 'Aula Virtual'
        ])->subject('Continúa tu registro en PVirtual');
    }
}
