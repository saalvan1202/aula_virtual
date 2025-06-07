<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpCode;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function username()
    {
        return 'email';
    }
    public function showLoginForm(Request $request)
    {

        return Inertia::render('Auth/Login',[
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        return array_merge($credentials, ['estado' => 'A']);
    }
    // protected function authenticated(Request $request, $user)
    // {
    //     if ($user->registro_completado === 'N' && $user->id_perfil === 5) {
    //         auth()->logout();

    //         return redirect()->route('login')
    //             ->withErrors([
    //                 'usuario' => 'Esta cuenta no existe en el sistema',
    //             ]);
    //     }
    // }
    public function login(Request $request)
    {
        $this->validateLogin($request);

        $user = User::where($this->username(), $request->{$this->username()})->first();

        if (!$user) {

            return back()->withErrors([
                $this->username() => 'Esta cuenta no existe en el sistema',
            ]);
        }

        if ($user->registro_completado === 'N' && $user->id_perfil === 5) {

            auth()->logout();
            
            return back()->withErrors([
                $this->username() => 'Esta cuenta no existe en el sistema',
            ]);
        }

        if ($user && Auth::validate($this->credentials($request))) {
            //Generar OTP y guardar
            $otp = rand(100000, 999999); // 6 dígitos
            $user->otp_code = $otp;
            $user->otp_expires_at = now()->addMinutes(5);
            $user->save();

            //Enviar OTP por correo
            Mail::to($user->email)->send(new OtpCode($user, $otp));

            //Guardar en sesión temporal
            session([
                'otp_user_id' => $user->id,
            ]);

           return redirect()->route('otp.form');
        }

            return back()->withErrors([
                $this->username() => 'Credenciales inválidas.',
            ]);
    }

    public function showOtpForm()
    {
        return Inertia::render('Auth/OtpVerify');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required',
        ]);

        $user = User::find(session('otp_user_id'));

        if (!$user || $user->otp_code !== $request->otp_code || now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp_code' => 'Código inválido o expirado.']);
        }

        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        Auth::login($user);

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
        ]);
    }
}
