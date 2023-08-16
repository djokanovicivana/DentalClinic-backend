<?php

use App\Http\Controllers\GranaController;
use App\Http\Controllers\UslugaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PregledController;
use App\Http\Controllers\PacijentController;

use Illuminate\Support\Facades\Route;

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
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE, PATCH, ANY");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With, Authorization"); // Dodajte 'Authorization' zaglavlje ako koristite autentifikaciju


Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [GranaController::class, 'index'])->name('grana.index');
Route::get('/usluge/{idGrana}',[UslugaController::class, 'index'])->name('usluga.index');
Route::get('/usluga/{idUsluga}',[UslugaController::class,'show'])->name('usluga.show');
Route::get('/pacijenti/{idDoktor}',[PregledController::class,'pacijentiDoktor']);
Route::get('/pacijent/{idPacijent}',[PacijentController::class,'pacijentId']);
Route::get('/terminiZavrseni/{idPacijent}',[TerminController::class,'terminiPacijentZavrseni']);
Route::get('/terminiBuduci/{idPacijent}',[TerminController::class,'terminiPacijentBuduci']);