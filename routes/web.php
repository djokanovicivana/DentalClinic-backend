<?php

use App\Http\Controllers\GranaController;
use App\Http\Controllers\UslugaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PregledController;
use App\Http\Controllers\PacijentController;
use App\Http\Controllers\TerminController;
use App\Http\Controllers\DoktorController;

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
Route::get('/uslugeDoktor/{idDoktor}',[UslugaController::class,'uslugeZaDoktora']);

Route::get('/pacijenti/{idDoktor}',[PregledController::class,'pacijentiDoktor']);
Route::get('/pacijent/{idPacijent}',[PacijentController::class,'pacijentId']);
Route::get('/terminiZavrseni/{idPacijent}/{idDoktor}',[TerminController::class,'terminiPacijentZavrseni']);
Route::get('/terminiBuduci/{idPacijent}/{idDoktor}',[TerminController::class,'terminiPacijentBuduci']);
Route::get('/termin/{terminId}',[TerminController::class,'terminId']);
Route::get('/pregled/{pregledId}',[PregledController::class,'pregledId']);
Route::get('/terminiZakazani/{idDoktor}/{idPacijent}',[TerminController::class,'terminiZakazani']);

Route::get('/doktor/{idDoktor}',[DoktorController::class,'show']);