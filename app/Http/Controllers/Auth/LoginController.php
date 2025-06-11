<?php

namespace App\Http\Controllers\Auth;
use Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request as HttpRequest;
use Request;


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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // Redirige al usuario a Google para autenticarse
    public function redirectToGoogle()
    {     
         // Guarda los datos del POST en la sesión
        
        return Socialite::driver('google')->redirect();
    }

    // Maneja la respuesta de Google después de la autenticación
    public function handleGoogleCallback()
    {
        // Obtiene los datos del usuario desde Google
        $googleUser = Socialite::driver('google')->user();

        // Busca si el usuario ya existe en la base de datos
        //$user = User::where('google_id', $googleUser->getId())->first();
        $user = User::where('email', $googleUser->getEmail())->first();

        // Si no existe, crea un nuevo usuario
        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        }

        // Inicia sesión al usuario
        Auth::login($user, true);

         // Recupera los datos del POST de la sesión
        $postData = session('post_data');
         // Mostrar los datos almacenados en la sesión
        //dd(session('post_data'));
       // dd($postData);
        // Si hay datos del POST, redirige a la ruta original con los datos
        if($postData) {
            session()->forget('post_data'); // Limpia los datos de la sesión
            return redirect()->intended(route('home'))->withInput($postData);
        }

        // Redirige al usuario a la página de inicio o cualquier otra página que desees
       // return redirect()->route('home');
       return redirect()->intended(route('home'));
    }   
    
   

    public function authenticated(HttpRequest $request, $user)
    {
         // Depuración: Verifica si la URL está en la sesión
        //dd(session('url.intended'));

        // Recupera los datos del POST de la sesión
        $postData = session('post_data');
        // Si hay datos del POST, redirige a la ruta original con los datos
        if($postData) {
            session()->forget('post_data'); // Limpia los datos de la sesión
            return redirect()->intended(route('home'))->withInput($postData);
        }
        // Redirige a la URL guardada antes del login, si existe, de lo contrario, al dashboard
        return redirect()->intended(route('home'));
    }


   
    
}
