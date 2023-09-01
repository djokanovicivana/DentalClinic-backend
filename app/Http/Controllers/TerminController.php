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
public function terminiZakazani($idDoktor, $idPacijent) {
    $now = Carbon::now();

    $termini = Termin::where('idKorisnika', $idDoktor)
        ->where('zakazan', 1)
        ->where(function ($query) use ($now) {
            $query->where('datumTermina', '<', $now)
                ->orWhere(function ($query) use ($now) {
                    $query->where('datumTermina', '=', $now)
                        ->where('vremeTermina', '<', $now->format('H:i:s'));
                });
        })
        ->join('pregled', 'pregled.idTermin', '=', 'termin.idTermin')
        ->where('pregled.obavljen', 0)
        ->where('pregled.idKorisnikPacijent', $idPacijent)
        ->where('pregled.idKorisnikDoktor', $idDoktor)
        ->distinct()
        ->get();

    return response()->json($termini);
}
public function getTerminiZaDoktora($idDoktora)
{
    $termini = Termin::where('idKorisnik', $idDoktora)->get();
    return response()->json($termini);
}
public function pretrazivanjeTermina(Request $request) {
    $query = Termin::query();
    $pocetniDatum = $request->input('pocetniDatum');
    $krajnjiDatum = $request->input('krajnjiDatum');
    $pocetnoVreme = $request->input('pocetnoVreme');
    $krajnjeVreme = $request->input('krajnjeVreme');
    $doktor = $request->input('doktor');
    $usluga=$request->input('usluga');

    if ($pocetniDatum && $krajnjiDatum) {
        $query->whereBetween('datumTermina', [$pocetniDatum, $krajnjiDatum]);
    } elseif ($pocetniDatum && !$krajnjiDatum) {
        $query->where('datumTermina', '>=', $pocetniDatum);
    } elseif ($krajnjiDatum && !$pocetniDatum) {
        $query->where('datumTermina', '<=', $krajnjiDatum);
    }

    if ($pocetnoVreme && $krajnjeVreme) {
        $query->where(function ($query) use ($pocetnoVreme, $krajnjeVreme) {
            $query->whereTime('vremeTermina', '>=', $pocetnoVreme)
                  ->whereTime('vremeTermina', '<=', $krajnjeVreme);
        });
    }elseif ($pocetnoVreme && !$krajnjeVreme)
    {
        $query->where(function ($query) use ($pocetnoVreme, $krajnjeVreme) {
            $query->where('vremeTermina', '>=', $pocetnoVreme);
    });}
    elseif (!$pocetnoVreme && $krajnjeVreme)
    {
        $query->where(function ($query) use ($pocetnoVreme, $krajnjeVreme) {
            $query->where('vremeTermina', '<=', $krajnjeVreme);
    });}
$termini = $query->join('korisnik as doktor', 'doktor.idKorisnik', '=', 'termin.idKorisnik')
    ->where(function ($subquery) use ($doktor) {
        $subquery->where('doktor.ime', 'LIKE', '%' . $doktor . '%')
            ->orWhere('doktor.prezime', 'LIKE', '%' . $doktor . '%');
    })
    ->groupBy('doktor.ime', 'doktor.prezime')
    ->get();


    if ($usluga) {
    $query->join('doktor', 'doktor.idKorisnik', '=', 'termin.idKorisnik')
          ->join('usluga', 'usluga.idGrana','=','doktor.idGrana')
          ->where('usluga.nazivUsluga', '=', $usluga);
   }

    

    
$termini = $query->join('korisnik', 'korisnik.idKorisnik', '=', 'termin.idKorisnik')
    ->select('korisnik.ime', 'korisnik.prezime', 'korisnik.slika', 'korisnik.idKorisnik', Termin::raw('GROUP_CONCAT(CONCAT(termin.idTermin, " ", termin.datumTermina, " ", termin.vremeTermina," ",termin.zakazan)) as termini'))
    ->groupBy('korisnik.ime', 'korisnik.prezime', 'korisnik.slika')
    ->get();



    return response()->json($termini);
}


}