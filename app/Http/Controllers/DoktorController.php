<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Doktor;
use App\Models\Korisnik;
use App\Models\Grana;

class DoktorController extends Controller
{
    public function show($idDoktor){
        $doktor=Doktor::join('korisnik','doktor.idKorisnik','=','korisnik.idKorisnik')
        ->where('korisnik.idKorisnik',$idDoktor)
        ->join('grana','doktor.idGrana','=','grana.idGrana')
        ->select('korisnik.*','doktor.*','grana.nazivGrana')
        ->get();
        return response()->json($doktor);
    }
}