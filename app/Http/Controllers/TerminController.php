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

    if ($request->has('pocetniDatum') && $request->has('krajnjiDatum')) {
        $pocetniDatum = $request->input('pocetniDatum');
        $krajnjiDatum = $request->input('krajnjiDatum');

        $query->whereBetween('datumTermina', [$pocetniDatum, $krajnjiDatum]);
    } elseif ($request->has('pocetniDatum')) {
        $pocetniDatum = $request->input('pocetniDatum');

        $query->where('datumTermina', '>=', $pocetniDatum);
    } elseif ($request->has('krajnjiDatum')) {
        $krajnjiDatum = $request->input('krajnjiDatum');

        $query->where('datumTermina', '<=', $krajnjiDatum);
    }

    if ($request->has('pocetnoVreme') && $request->has('krajnjeVreme')) {
        $pocetnoVreme = $request->input('pocetnoVreme');
        $krajnjeVreme = $request->input('krajnjeVreme');

        $query->where(function ($query) use ($pocetnoVreme, $krajnjeVreme) {
            $query->where('vremeTermina', '>=', $pocetnoVreme)
                  ->where('vremeTermina', '<=', $krajnjeVreme);
        });
    }
     if ($request->has('doktor') && !$request->has('usluga')) {
        $doktor = $request->input('doktor');
        $query->join('korisnik', 'korisnik.idKorisnik', '=', 'termin.idKorisnik')
            ->where(function ($subquery) use ($doktor) {
                $subquery->where('korisnik.ime', 'like', '%' . $doktor . '%')
                    ->orWhere('korisnik.prezime', 'like', '%' . $doktor . '%');
            });
    }
    
if ($request->has('doktor') && $request->has('usluga')) {
    $doktor = $request->input('doktor');
    $nazivUsluge = $request->input('usluga');

    $query->join('korisnik', 'korisnik.idKorisnik', '=', 'termin.idKorisnik')
        ->join('usluga', 'usluga.idGrana', '=', 'korisnik.idGrana')
        ->where(function ($subquery) use ($doktor) {
            $subquery->where('korisnik.ime', 'like', '%' . $doktor . '%')
                ->orWhere('korisnik.prezime', 'like', '%' . $doktor . '%');
        })
        ->where('usluga.nazivUsluga', 'like', '%' . $nazivUsluge . '%');

    $count = $query->count();

    if ($count === 0) {
        return response()->json(['message' => 'Izabrani doktor ne obavlja izabranu uslugu'], 400);
    }
    
    $termini = $query->get();

    return response()->json($termini);
}


}}