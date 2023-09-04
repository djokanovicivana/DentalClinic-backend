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
Route::get('/uslugaTermin/{idTermina}',[UslugaController::class,'uslugaIdTermina']);
Route::get('/sveUsluge',[UslugaController::class,'sveUsluge']);

Route::get('/pacijenti/{idDoktor}',[PacijentController::class,'pacijentiDoktor']);
Route::get('/pacijent/{idPacijent}',[PacijentController::class,'pacijentId']);

Route::get('/terminiZavrseni/{idPacijent}/{idDoktor}',[TerminController::class,'terminiPacijentZavrseni']);
Route::get('/terminiBuduci/{idPacijent}/{idDoktor}',[TerminController::class,'terminiPacijentBuduci']);
Route::get('/termin/{terminId}',[TerminController::class,'terminId']);
Route::get('/terminiDoktor/{idDoktora}',[TerminController::class,'getTerminiZaDoktora']);

Route::get('/pregled/{pregledId}',[PregledController::class,'pregledId']);
Route::get('/pregledTermin/{idTermin}',[PregledController::class,'pregledIdTermin']);
Route::get('/terminiZakazani/{idDoktor}/{idPacijent}',[TerminController::class,'terminiZakazani']);
Route::get('/pretrazivanjeTermina',[TerminController::class,'pretrazivanjeTermina']);
Route::get('/obavljeniPreglediDoktor/{idDoktora}/{nazivUsluga}',[PregledController::class,'obavljeniPreglediDoktor']);
Route::get('/obavljeniPreglediPacijent/{idPacijenta}/{nazivUsluga}',[PregledController::class,'obavljeniPreglediPacijent']);
Route::get('/predstojeciPreglediDoktor/{idDoktora}/{nazivUsluga}',[PregledController::class,'predstojeciPreglediDoktor']);
Route::get('/predstojeciPreglediPacijent/{idPacijenta}/{nazivUsluga}',[PregledController::class,'predstojeciPreglediPacijent']);
Route::get('/brojObavljeniDoktor/{idDoktora}',[PregledController::class,'brojObavljeniDoktor']);
Route::get('/brojObavljenihPacijent/{idPacijenta}',[PregledController::class,'brojObavljenihPacijent']);
Route::get('/doktor/{idDoktor}',[DoktorController::class,'show']);