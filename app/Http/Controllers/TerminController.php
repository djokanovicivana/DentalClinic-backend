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
 public function pretrazivanjeTermina(Request $request)
    { 
          $pocetniDatum = $request->input('pocetniDatum') ?? null;
        $krajnjiDatum = $request->input('krajnjiDatum') ?? null;
        $pocetnoVreme = $request->input('pocetnoVreme') ?? null;
        $krajnjeVreme = $request->input('krajnjeVreme') ?? null;
        $doktor = $request->input('doktor') ?? null;
        $usluga = $request->input('usluga') ?? null;
        if (!$pocetniDatum && !$krajnjiDatum && !$pocetnoVreme && !$krajnjeVreme && !$doktor && !$usluga) {

        // Ako nema kriterijuma pretrage, nema potrebe za dodatnim uslovima
        $termini = Termin::join('korisnik', 'korisnik.idKorisnik', '=', 'termin.idKorisnik')
            ->select('korisnik.ime', 'korisnik.prezime', 'korisnik.slika', 'korisnik.idKorisnik', Termin::raw('GROUP_CONCAT(CONCAT(termin.idTermin, " ", termin.datumTermina, " ", termin.vremeTermina," ",termin.zakazan)) as termini'))
            ->groupBy('korisnik.ime', 'korisnik.prezime', 'korisnik.slika')
            ->get();
    } else {
         
        $query = Termin::query();

        if ($pocetniDatum) {
            $query->where('datumTermina', '>=', $pocetniDatum);
        }
        if ($krajnjiDatum) {
            $query->where('datumTermina', '<=', $krajnjiDatum);
        }
        if ($pocetnoVreme) {
            $query->whereTime('vremeTermina', '>=', $pocetnoVreme);
        }
        if ($krajnjeVreme) {
            $query->whereTime('vremeTermina', '<=', $krajnjeVreme);
        }
        if ($doktor) {
            $query->where(function ($subquery) use ($doktor) {
                $subquery->where('ime', 'LIKE', '%' . $doktor . '%')
                    ->orWhere('prezime', 'LIKE', '%' . $doktor . '%');
            });
        }
        
        // Dodajte uslov za uslugu ako je dostupna
        if ($usluga) {
            $query->join('doktor', 'doktor.idKorisnik', '=', 'termin.idKorisnik')
                  ->join('usluga', 'usluga.idGrana', '=', 'doktor.idGrana')
                  ->where('usluga.nazivUsluga', '=', $usluga);
        }

        // Dohvatite podatke iz baze
        $termini = $query->select('korisnik.ime', 'korisnik.prezime', 'korisnik.slika', 'korisnik.idKorisnik', Termin::raw('GROUP_CONCAT(CONCAT(termin.idTermin, " ", termin.datumTermina, " ", termin.vremeTermina," ",termin.zakazan)) as termini'))
            ->join('korisnik', 'korisnik.idKorisnik', '=', 'termin.idKorisnik')
            ->groupBy('korisnik.ime', 'korisnik.prezime', 'korisnik.slika')
            ->get();
    }
        return response()->json($termini);
    }
    



}