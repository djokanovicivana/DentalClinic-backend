<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KorisnikController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PregledController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE, PATCH, ANY");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With, Authorization"); 

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login',[AuthController::class,'login'])->name('auth.login');
Route::post('/registracija',[KorisnikController::class,'create']);
Route::patch('/noviPregled/{idKorisnikPacijent}/{idKorisnikDoktor}/{idTermin}',[PregledController::class,'noviPregled']);