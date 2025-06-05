<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TokensCorreo;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContinuarRegistro; // Asegúrate de crear este Mailable


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function index()
    {
        return Inertia::render('Auth/Register');
    }

    public function tokenError()
    {
        return Inertia::render('Auth/TokenError');
    }

    public function verificarDniEmail(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|size:8|regex:/^\d{8}$/',
            'email' => 'required|email',
        ], []);

        // Buscar al usuario por DNI y correo
        $user = User::whereHas('persona', function ($q) use ($request) {
            $q->where('numero_documento', $request->dni);
        })->where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Este usuario no se encuentra matriculado.',
            ], 400);
        }

        if ($user->registro_completado === 'S') {
            return response()->json([
                'message' => 'Este usuario ya se encuentra registrado en el sistema.',
            ], 400);
        }

        TokensCorreo::where('usuario_id', $user->id)->delete();

        // Generar nuevo token
        $token = \Str::random(60);

        // Guardar nuevo token
        TokensCorreo::create([
            'usuario_id' => $user->id,
            'token' => $token,
        ]);


        // Enviar correo con el enlace
        Mail::to($user->email)->send(new ContinuarRegistro($user, $token));

        return response()->json([
            'message' => 'Se ha enviado un enlace a tu correo para continuar el registro.',
        ], 200);
    }

    public function continuarRegistro($token)
    {
        // Verificar si el token existe
        $tokenRecord = TokensCorreo::where('token', $token)->first();

        if (!$tokenRecord) {
            return redirect()->route('register.token-error')->withErrors(['token' => 'El error se produjo debido a: Token inválido.']);
        }

        $createdAt = $tokenRecord->created_at;
        $now = Carbon::now();

        if ($now->diffInMinutes($createdAt) > 5) {
            return redirect()->route('register.token-error')->withErrors(['token' => 'El error se produjo debido a: Token expirado.']);
        }

        // Obtener el usuario asociado al token
        $user = User::with('persona')->find($tokenRecord->usuario_id);

        if (!$user) {
            return redirect()->route('register.token-error')->withErrors(['token' => 'El error se produjo debido a: Usuario no encontrado.']);
        }

        // Aquí puedes redirigir al usuario a una vista para completar su registro
        return Inertia::render('Auth/RegisterContinue', ['user' => $user, 'token' => $token]);
    }

    public function finalizarRegistro($token, Request $request)
    {
        $tokenRecord = TokensCorreo::where('token', $token)->first();

        if (!$tokenRecord) {
            return redirect()->route('register.index')->withErrors(['token' => 'Token inválido.']);
        }

        $createdAt = $tokenRecord->created_at;
        $now = Carbon::now();

        if ($now->diffInMinutes($createdAt) > 5) {
            $tokenRecord->delete();
            return redirect()->route('register.index')->withErrors(['token' => 'Token expirado.']);
        }

        $user = User::with('persona')->findOrFail($tokenRecord->usuario_id);

        $request->validate([
            'nombres' => ['required', 'string', 'min:1' , 'max:150', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u'],
            'apellido_paterno' => ['required', 'string', 'min:1' , 'max:150', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u'],
            'apellido_materno' => ['required', 'string', 'min:1' , 'max:150', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u'],
            'sexo' => ['required', 'string', 'in:M,F'],
            'password' => [
                'required',
                'string',
                Password::min(8)    
                    ->mixedCase()     
                    ->numbers()    
                    ->symbols(),      
                'confirmed',           
            ],
        ],[], [
            'password' => 'contraseña',
        ]);

        if ($user->persona) {
            $user->persona->update([
                'nombres' => $request->nombres,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'sexo' => $request->sexo,
            ]);
        } else {
            return redirect()->route('register.index')->withErrors(['general' => 'No se encontró la información personal asociada al usuario.']);
        }


        $user->password = bcrypt($request->password);
        $user->registro_completado = 'S';
        $user->save();

        $tokenRecord->delete();        

        return response()->json(['message' => 'Registro finalizado con éxito. Su cuenta ha sido activada.'], 200);
    }

}
