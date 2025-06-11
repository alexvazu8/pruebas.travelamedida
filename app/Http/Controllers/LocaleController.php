<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

use function Illuminate\Log\log;

class LocaleController extends Controller
{
        /**
     * Cambia el idioma de la aplicación.
     *
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch($locale)
    {  
        // Validar el idioma permitido
        if (!preg_match('/^[a-z]{2}$/', $locale)) {
            Log::warning('Idioma no válido', ['locale' => $locale]);
            return back()->withErrors(['language' => __('Idioma no disponible')]);
        }

        // Guardar en sesión
        //Session::put('locale', $locale);
        //session()->put('locale', $locale);
        // En un controlador o middleware
        session(['locale' => $locale]);


        // Opcional: aplicar directamente el locale en esta request
        App::setLocale($locale);
        
        // Log de la sesión (opcional)
        $sessionId = session()->getId();
       $sessionPath = storage_path('framework/sessions/'.$sessionId);

        Log::info('Sesión actualizada para idioma', [
            'session_id' => $sessionId,
            'locale' => session('locale'),
        ]);
        //Session::regenerate();
        // Redirigir de regreso con la cookie actualizada
        return redirect()->back();
        //return view('welcome');
    }

    /**
     * Verifica si el idioma es válido.
     *
     * @param  string  $locale
     * @return bool
     */
    protected function isValidLocale($locale)
    {
        return array_key_exists(
            Str::lower($locale), 
            config('app.available_locales', [])
        );
    }
}
