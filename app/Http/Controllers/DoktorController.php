<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Grana;
use App\Models\Doktor;

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
    public function sviDoktori(Request $request, $nazivGrana){
        $searchTerm=$request->input('searchTerm');
        $query = Doktor::join('korisnik', 'doktor.idKorisnik', '=', 'korisnik.idKorisnik')
            ->join('grana','doktor.idGrana','=','grana.idGrana')
            ->where('grana.nazivGrana',$nazivGrana)
            ->select('korisnik.*','doktor.*','grana.nazivGrana')
            ->distinct();

        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('korisnik.ime', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('korisnik.prezime', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $doktori = $query->get();

        return response()->json($doktori);
     }
}