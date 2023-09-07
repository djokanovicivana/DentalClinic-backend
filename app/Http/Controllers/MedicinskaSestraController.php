<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicinskaSestra;
use App\Models\Korisnik;
use App\Models\Doktor;
use App\Models\Grana;

class MedicinskaSestraController extends Controller
{
        public function sveSestre(Request $request, $nazivGrana){
        $searchTerm = $request->input('searchTerm');
        $query = MedicinskaSestra::join('korisnik', 'medicinskaSestra.idKorisnik', '=', 'korisnik.idKorisnik')
        ->join('doktor','medicinskaSestra.idDoktor','=','doktor.idKorisnik')
        ->join('grana','doktor.idGrana','=','grana.idGrana')
        ->where('grana.nazivGrana',$nazivGrana)
        ->select('korisnik.*')
        ->distinct();

        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('korisnik.ime', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('korisnik.prezime', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $sestre = $query->get();

        return response()->json($sestre);
     }
      public function sestraId($sestraId){
        $sestra=MedicinskaSestra::join('korisnik','korisnik.idKorisnik','=','medicinskaSestra.idKorisnik')
        ->where('medicinskaSestra.idKorisnik', $sestraId)
        ->select('korisnik.*','medicinskaSestra.idDoktor')
        ->get();

        return response()->json($sestra);
    }
}