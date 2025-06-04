<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
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
        return 'usuario';
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
    protected function authenticated(Request $request, $user)
    {
        if ($user->registro_completado === 'N' && $user->id_perfil === 5) {
            auth()->logout();

            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Esta cuenta no existe en el sistema',
                ]);
        }
    }
}
