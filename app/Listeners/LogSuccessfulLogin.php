<?php

namespace App\Listeners;

use App\Models\Login as LoginModel;
use Illuminate\Http\Request;

class LogSuccessfulLogin
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request=$request;
    }

    public function handle($event)
    {
        set_time_limit(0);
        $user = $event->user;
        $ip = $this->request->ip();
        if(mb_strlen($ip)<5){
            $ip='127.0.0.1';
        }
        $userAgent = $this->request->userAgent();
        $login=new LoginModel();
        $login->id_usuario=$user->getAuthIdentifier();
        $login->usuario=$user->usuario;
        $login->direccion_ip=$ip;
        $login->dispositivo=$userAgent;
        $login->estado='A';
        $login->save();
    }
}
