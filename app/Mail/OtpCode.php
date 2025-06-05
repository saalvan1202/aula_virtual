<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class OtpCode extends Mailable
{
    public $user;
    public $otp;

    public function __construct($user, $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    public function build()
    {
        $logo = asset('logo.png');  // ruta pública del logo
     return $this->subject('Código de verificación 2FA')
                    ->view('mails.otp');
    }
}
