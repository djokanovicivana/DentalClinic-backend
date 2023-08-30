<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usluga;
use App\Models\Grana;
use App\Models\Doktor;

class UslugaController extends Controller
{
    public function index($id){
        $usluge=Usluga::where('idGrana',$id)->get();
    return response()->json($usluge);
    }
    public function show($id){
        $usluga=Usluga::where('idUsluga',$id)->get();
        return response()->json($usluga);
    }
    public function uslugeZaDoktora($idDoktor)
{
    $usluge = Usluga::join('doktor', 'usluga.idGrana', '=', 'doktor.idGrana')
    ->where('doktor.idKorisnik', $idDoktor)
    ->select('usluga.nazivUsluga','usluga.idUsluga')
    ->get();

    return response()->json($usluge);
}
public function uslugaIdTermina($idTermin) {
    $pregled = Pregled::join('usluga', 'pregled.idUsluga', '=', 'usluga.idUsluga')
        ->where('pregled.idTermin', $idTermin)
        ->first();

    if (!$pregled) {
        return null; 
    }

    return [
        'idUsluga' => $pregled->idUsluga,
        'nazivUsluga' => $pregled->nazivUsluga
    ];
}







}