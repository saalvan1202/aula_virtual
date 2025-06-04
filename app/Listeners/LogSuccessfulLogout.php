<?php

namespace App\Listeners;



use App\Models\Login;
use Illuminate\Http\Request;

class LogSuccessfulLogout
{

    protected $request;

    public function __construct(Request $request)
    {
        $this->request=$request;
    }

    public function handle($event)
    {
        set_time_limit(0);
        if ($event->user) {
            $user = $event->user;
            $ip = $this->request->ip();
            if(mb_strlen($ip)<5){
                $ip='127.0.0.1';
            }
            $userAgent = $this->request->userAgent();
            $login=Login::where('id_usuario',$user->getAuthIdentifier())
                ->where('direccion_ip',$ip)
                ->where('dispositivo',$userAgent)
                ->whereNull('logout_at')
                ->orderBy('id','desc')
                ->first();
            if($login){
                $login->logout_at=now()->format('Y-m-d H:i:s');
                $login->save();
            }
        }
    }
}
