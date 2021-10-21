<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Ruta home, la muestra si esta logeado, si no lo redirige a login
Route::get('/', function () {
    return view('home');
})->middleware('auth');

Auth::routes();

// Resource de rutas para el controlador de SyncHorario
Route::resource('synchorarios', App\Http\Controllers\SynchorarioController::class)->middleware('auth');
// Resource de rutas para el controlador de Conexionftp
Route::resource('conexionesftps', App\Http\Controllers\ConexionesftpController::class)->middleware('auth');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rutas jobs
Route::get('aseguradora', 'App\Http\Controllers\AseguradoraController@ImportarDatosAseguradora');
Route::get('autonomi', 'App\Http\Controllers\EjecucionesController@EjecutarAutonomi');
Route::get('mascotasBP', 'App\Http\Controllers\EjecucionesController@EjecutarMascotasBP');
Route::get('integral', 'App\Http\Controllers\EjecucionesController@EjecutarIntegral');
Route::get('sagrario', 'App\Http\Controllers\EjecucionesController@EjecutarSagrario');
Route::get('sagrarioh', 'App\Http\Controllers\EjecucionesController@EjecutarSagrarioHijos');
Route::get('linkmatic', 'App\Http\Controllers\EjecucionesController@EjecutarLinkmatic');
Route::get('autofenix', 'App\Http\Controllers\EjecucionesController@EjecutarAutofenix');
Route::get('equinorte', 'App\Http\Controllers\EjecucionesController@EjecutarEquinorte');
Route::get('hyunmotor', 'App\Http\Controllers\EjecucionesController@EjecutarHyunmotor');
Route::get('vallejoaraujo', 'App\Http\Controllers\EjecucionesController@EjecutarVallejoAraujo');
Route::get('palig', 'App\Http\Controllers\EjecucionesController@EjecutarPalig');
Route::get('vehiculos', 'App\Http\Controllers\VehiculosController@ImportarDatos');

// Ruta para mostrar los jobs (se comento en el layout el boton para acceder)
Route::get('/jobs/index', function () {
    return view('jobs.index');
})->middleware('auth');
