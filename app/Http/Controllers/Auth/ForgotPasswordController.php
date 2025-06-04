<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RestablecerPassword;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
    protected $sendMail;

    public function showLinkRequestForm(Request $request)
    {
        return Inertia::render('Auth/ForgotPassword',[
            'status' => $request->session()->get('status'),
            'fail'=>$request->session()->get('fail'),
        ]);
    }
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);
        $response = $this->broker()->sendResetLink(
            $this->credentials($request),
            function($user, $token) use ($request) {
                return $this->sendNotificationEmail($user, $token,$request);
            }
        );
        if(!$this->sendMail){
            if ($request->wantsJson()) {
                throw ValidationException::withMessages([
                    'fail' => ['ocurrio un error al enviar el correo intentelo mas tarde'],
                ]);
            }
            return back()
                ->withInput($request->only('email'))
                ->with('fail','ocurrio un error al enviar el correo intentelo mas tarde');
        }
        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }
    public function sendNotificationEmail($user,$token,$request)
    {
        set_time_limit(0);
        try {
           Mail::to($user->email)->send(
               new RestablecerPassword($user,$token)
           );
           $this->sendMail=true;
        }catch (\Exception $e){
            Log::info('ERROR_MAIL_RESTABLECER_PASSWORD',[
                'file'=>$e->getFile(),
                'code'=>$e->getCode(),
                'message'=>$e->getMessage(),
            ]);
            $this->sendMail=false;
        }
    }
}
