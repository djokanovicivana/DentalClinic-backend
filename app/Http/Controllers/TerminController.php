<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Termin;
use App\Models\Pacijent;
use App\Models\Pregled;

class TerminController extends Controller
{
    public function terminiPacijentZavrseni($idPacijent){
        $termini=Termin::join('pacijent','pacijent.idKorisnik','=','termin.idKorisnik')
        ->join('pregled', 'termin.idTermin', '=', 'pregled.idTermin')
        ->where('pregled.idKorisnikPacijent',$idPacijent)
        ->where('termin.zakazan',1)
        ->select('termin.datumTermina','termin.vremeTermina')
        ->get();
        
        return response()->json($termini);
    }
    public function terminiPacijentBuduci($idPacijent){
    $termini = Termin::join('pacijent', 'pacijent.idKorisnik', '=', 'termin.idKorisnik')
        ->join('pregled', 'termin.idTermin', '=', 'pregled.idTermin')
        ->where('pregled.idKorisnikPacijent', $idPacijent)
        ->where('pregled.zavrsen', 0)
        ->where('termin.zakazan',1)
        ->where(function ($query) {
            $query->where('termin.datumTermina', '>', now()->toDateString())
                  ->orWhere(function ($query) {
                      $query->where('termin.datumTermina', now()->toDateString())
                            ->where('termin.vremeTermina', '>', now()->toTimeString());
                  });
        })
        ->select('termin.datumTermina', 'termin.vremeTermina')
        ->get();
    
    return response()->json($termini);
}

}