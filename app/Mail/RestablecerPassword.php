<?php

namespace App\Mail;

use App\Models\Instituto;
use App\Services\InstitutoImagen;
use App\Services\Variable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RestablecerPassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $token;
    public function __construct($user,$token)
    {
        $this->user=$user;
        $this->token=$token;
    }

    public function build()
    {
        $instituto=Instituto::where('estado','A')->first();
        $imagenes=(new InstitutoImagen())->getArray();
        return $this->view('mails.restablecer-password',[
            'url'=>$this->resetUrl(),
            'app_name'=>Variable::appName(),
            'usuario'=>$this->user->persona->apellidos_completos,
            'logo'=>$imagenes['logo'],
        ])->subject('Notificación de restablecimiento de contraseña')
            ->from($instituto->getEmailNotification(),'pvirtual');
    }
    public function resetUrl()
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->user->getEmailForPasswordReset(),
        ], false));
    }
}
