<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Pagos\StereumPayController;
use App\Http\Controllers\CarritosController;
use App\Http\Controllers\HotelesController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ReservasController;
use App\Http\Controllers\ToursController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\TrasladosController;
use App\Http\Middleware\CapturePostData;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('login/google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [LoginController::class, 'handleGoogleCallback']);


//traslados
Route::get('/traslados', [TrasladosController::class, 'index'])->name('traslados.index');
//Route::post('/traslados/obtener', [TrasladosController::class, 'obtenerDisponibilidad'])->name('traslados.obtener');
Route::match(['get', 'post'], '/traslados/obtener', [TrasladosController::class, 'obtenerDisponibilidad'])->name('traslados.obtener');
Route::get('/traslados/zonas-origen/{idciudad}/{tipo_servicio_transfer}', [TrasladosController::class, 'getZonasOrigen']);
Route::get('/traslados/zonas-destino/{idzona}', [TrasladosController::class, 'getZonasDestinoPorOrigen']);

//hoteles
Route::get('/hoteles', [HotelesController::class, 'index'])->name('hoteles.index');
Route::match(['get', 'post'],'/hoteles/obtener', [HotelesController::class, 'obtenerDisponibilidad'])->name('hoteles.obtener');
Route::get('/hoteles/info/{id}', [HotelesController::class, 'hotelInfo'])->name('hoteles.info');

//tours
Route::get('/tours', [ToursController::class, 'index'])->name('tours.index');
Route::match(['get', 'post'],'/tours/obtener', [ToursController::class, 'obtenerDisponibilidad'])->name('tours.obtener');
Route::get('/tours/info/{id}', [ToursController::class, 'tourInfo'])->name('tour.info');


Route::get('/carritos/show', [CarritosController::class, 'show'])->name('carritos.show')->middleware('auth');
Route::delete('/carritos/borrar', [CarritosController::class, 'borrar'])->name('carritos.borrar')->middleware('auth');

Route::post('/traslados/addCarrito', [TrasladosController::class, 'addCarritoTraslados'])->name('traslados.addCarrito');
Route::post('/hoteles/addCarrito', [HotelesController::class, 'addCarritoHotel'])->name('hoteles.addCarrito')->middleware('guardaPost');
Route::post('/tours/addCarrito', [ToursController::class, 'addCarritoTours'])->name('tours.addCarrito');

Route::post('/reservas/confirmar', [ReservasController::class, 'confirmar'])->name('reservas.confirmar')->middleware(['auth','verifyPayment']);
Route::get('/reservas/showReserva/{loc}', [ReservasController::class, 'showReserva'])->name('reservas.showReserva')->middleware('auth');
Route::resource('reservas', ReservaController::class)->middleware('auth');

//Footer
Route::get('/nosotros', function () {
    return view('nosotros');
})->name('nosotros');

Route::get('/terminos', function () {
    return view('terminos');
})->name('terminos');

Route::get('/privacidad', function () {
    return view('privacidad');
})->name('privacidad');

Route::get('/reservas/voucher/{id}', [ReservasController::class, 'voucher'])->name('reservas.voucher')->middleware('auth');

Route::get('lang/{locale}', [LocaleController::class, 'switch'])
    ->name('lang.switch')
    ->where('locale', '[a-z]{2}');  // Solo acepta 2 letras (es, en, pt)
    

Route::get('/stereumpay/login', [StereumPayController::class, 'login'])->middleware('auth');
Route::post('/pagos/charge', [StereumPayController::class, 'createCharge'])->name('pagos.charge')->middleware('auth');
Route::post('/pagos/status/{transactionId}', [StereumPayController::class, 'checkPaymentStatus'])->name('pagos.status')->middleware('auth');



