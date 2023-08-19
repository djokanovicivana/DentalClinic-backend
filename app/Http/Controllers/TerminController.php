<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Termin;
use App\Models\Pacijent;
use App\Models\Pregled;
use Carbon\Carbon;

class TerminController extends Controller
{
    public function terminId($idTermin){
        $termin=Termin::where('idTermin',$idTermin)->get();
        return response()->json($termin);
    }
    public function terminiPacijentZavrseni($idPacijent, $idDoktor){
        $termini=Termin::join('pregled', 'termin.idTermin', '=', 'pregled.idTermin')
        ->where('pregled.idKorisnikDoktor',$idDoktor)
        ->where('pregled.idKorisnikPacijent',$idPacijent)
        ->where('termin.zakazan', 1)
        ->where('pregled.obavljen',1)
        ->select('termin.datumTermina','termin.vremeTermina','pregled.idPregled')
        ->get();
        
        return response()->json($termini);
    }
    public function terminiPacijentBuduci($idPacijent,$idDoktor){
    $termini = Termin::join('pregled', 'termin.idTermin', '=', 'pregled.idTermin')
        ->where('pregled.idKorisnikPacijent', $idPacijent)
        ->where('pregled.idKorisnikDoktor',$idDoktor)
        ->where('pregled.obavljen', 0)
        ->where('termin.zakazan',1)
        ->where(function ($query) {
            $query->where('termin.datumTermina', '>', now()->toDateString())
                  ->orWhere(function ($query) {
                      $query->where('termin.datumTermina', now()->toDateString())
                            ->where('termin.vremeTermina', '>', now()->toTimeString());
                  });
        })
        ->select('termin.datumTermina', 'termin.vremeTermina','pregled.idPregled')
        ->get();
    
    return response()->json($termini);
}
public function terminiZakazani($idDoktor,$idPacijent){
    $now = Carbon::now();

    $termini = Termin::where('idKorisnika', $idDoktor)
        ->where('zakazan',1)
        ->where(function ($query) use ($now) {
            $query->where('datumTermina', '<', $now)
                  ->orWhere(function ($query) use ($now) {
                      $query->where('datumTermina', '=', $now)
                            ->where('vremeTermina', '<', $now->format('H:i:s'));
                  });
        })
        ->join('pregled','pregled.idTermin','=','termin.idTermin')
        ->where('pregled.obavljen',0)
        ->where('pregled.idKorisnikPacijent',$idPacijent)
        ->where('pregled.idKorisnikDoktor',$idDoktor)
        ->get();

    return response()->json($termini);
}

}