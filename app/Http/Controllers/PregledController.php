<?php
namespace App\Http\Controllers;

 use App\Http\Controllers\Exception;

use App\Models\Pacijent;
use App\Models\Pregled;
use App\Models\Korisnik;
use App\Models\Usluga;
use App\Models\Termin;
use Illuminate\Http\Request;



class PregledController extends Controller
{
   
    public function pregledId($pregledId){
        $pregled=Pregled::where('idPregled',$pregledId)->get();
        return response()->json($pregled);
    }
 public function noviPregled(Request $request, $idKorisnikPacijent, $idKorisnikDoktor, $idTermin) {
   
    $pregled = Pregled::where('idKorisnikPacijent', $idKorisnikPacijent)
                      ->where('idKorisnikDoktor', $idKorisnikDoktor)
                      ->where('idTermin', $idTermin)
                      ->first();

    if (!$pregled) {
        return response()->json(['message' => 'Pregled nije pronađen'], 404);
    }
    $pregled->idUsluga = $request->input('idUsluga');
    $pregled->anamneza = $request->input('anamneza');
    $pregled->dijagnoza = $request->input('dijagnoza');
    $pregled->lecenje = $request->input('lecenje');
    $pregled->obavljen = 1; 

    $pregled->save();

    return response()->json(['message' => 'Pregled je uspešno ažuriran'], 200);
}
public function pregledIdTermin($idTermin){
    $informacije=Pregled::where('pregled.idTermin', $idTermin)
    ->join('korisnik', 'korisnik.idKorisnik','=','pregled.idKorisnikPacijent')
    ->join('usluga','usluga.idUsluga','=','pregled.idUsluga')
    ->select('korisnik.ime','korisnik.prezime','usluga.nazivUsluga')
    ->get();
    return response()->json($informacije);
}

public function zakaziPregled(Request $request){
    try {
        $pregled = new Pregled();
        $nazivUsluga=$request->input('nazivUsluga');
        $idUsluga = Usluga::where('nazivUsluga', $nazivUsluga)
        ->select('usluga.idUsluga')
        ->first();
        
        $pregled->idTermin = $request->input('idTermin');
        $pregled->idKorisnikDoktor = $request->input('idKorisnikDoktor');
        $pregled->idKorisnikPacijent = $request->input('idKorisnikPacijent');
        $pregled->idUsluga = $idUsluga->idUsluga;
        $pregled->obavljen = 0; 
       
        $pregled->save();
        
        Termin::where('idTermin', $request->input('idTermin'))->update(['zakazan' => 1]);


      return response()->json(['idUsluga' => $idUsluga]);
    } catch (\Exception $e) {

        return response()->json(['success' => false, 'message' => 'Došlo je do greške prilikom zakazivanja pregleda.', 'error' => $e->getMessage()]);
    }
}

    public function obavljeniPreglediDoktor( $idDoktora, $nazivUsluge)
    {
            $pregledi=Pregled::join('usluga','pregled.idUsluga','=','usluga.idUsluga')
            ->where('usluga.nazivUsluga',$nazivUsluge)
            ->join('termin','termin.idTermin','=','pregled.idTermin')
            ->join('doktor','doktor.idKorisnik','=','pregled.idKorisnikDoktor')
            ->where('doktor.idKorisnik',$idDoktora)
            ->where('pregled.obavljen',1)
            ->join('korisnik','korisnik.idKorisnik','=','pregled.idKorisnikPacijent')
            ->select('korisnik.ime','korisnik.prezime','termin.datumTermina','termin.vremeTermina', 'usluga.nazivUsluga','pregled.idPregled')
             ->orderBy('termin.datumTermina', 'asc')
             ->orderBy('termin.vremeTermina', 'asc')
             ->get();

        $brojPregleda = $pregledi->count();

        return [
            'brojPregleda' => $brojPregleda,
            'pregledi' => $pregledi,
        ];
    }
     public function brojObavljeniDoktor($idDoktora)
    {
        return Pregled::where('idKorisnikDoktor', $idDoktora)
            ->where('obavljen', 1) // Samo obavljeni pregledi
            ->count();
    }
    public function predstojeciPreglediDoktor($idDoktora, $nazivUsluge)
{
    $pregledi = Pregled::join('usluga', 'pregled.idUsluga', '=', 'usluga.idUsluga')
        ->where('usluga.nazivUsluga', $nazivUsluge)
        ->join('termin', 'termin.idTermin', '=', 'pregled.idTermin')
        ->join('doktor', 'doktor.idKorisnik', '=', 'pregled.idKorisnikDoktor')
        ->where('doktor.idKorisnik', $idDoktora)
        ->where(function ($query) {
            $query->where('pregled.obavljen', 0) // Samo nepregledani pregledi
                  ->where(function ($query) {
            $query->where('termin.datumTermina', '>', now()) // Samo termini u budućnosti
                ->orWhere(function ($query) {
                    $query->where('termin.datumTermina', now())
                          ->where('termin.vremeTermina', '>=', now());
                });
                  });
        })
        ->join('korisnik', 'korisnik.idKorisnik', '=', 'pregled.idKorisnikPacijent')
        ->select(
            'korisnik.ime',
            'korisnik.prezime',
            'termin.datumTermina',
            'termin.vremeTermina',
            'usluga.nazivUsluga',
            'pregled.idPregled'
        )
        ->orderBy('termin.datumTermina', 'asc')
        ->orderBy('termin.vremeTermina', 'asc')
        ->get();

    $brojPregleda = $pregledi->count();

    return [
        'brojPregleda' => $brojPregleda,
        'pregledi' => $pregledi,
    ];
}
public function obavljeniPreglediPacijent($idPacijenta, $nazivUsluge)
{
    $pregledi = Pregled::join('usluga', 'pregled.idUsluga', '=', 'usluga.idUsluga')
        ->where('pregled.idKorisnikPacijent', $idPacijenta)
        ->where('pregled.obavljen', 1)
        ->where('usluga.nazivUsluga', $nazivUsluge)
        ->join('termin', 'termin.idTermin', '=', 'pregled.idTermin')
        ->join('korisnik', 'korisnik.idKorisnik', '=', 'pregled.idKorisnikDoktor')
        ->select(
            'korisnik.idKorisnik',
            'korisnik.ime',
            'korisnik.prezime',
            'termin.datumTermina',
            'termin.vremeTermina',
            'usluga.nazivUsluga',
            'pregled.idPregled',
        )
        ->orderBy('termin.datumTermina', 'asc')
        ->orderBy('termin.vremeTermina', 'asc')
        ->get();

    $brojPregleda = $pregledi->count();

    return [
        'brojPregleda' => $brojPregleda,
        'pregledi' => $pregledi,
    ];
}

public function brojObavljenihPacijent($idPacijenta)
{
    return Pregled::join('usluga', 'pregled.idUsluga', '=', 'usluga.idUsluga')
        ->where('pregled.idKorisnikPacijent', $idPacijenta)
        ->where('pregled.obavljen', 1)
        ->count();
}

public function predstojeciPreglediPacijent($idPacijenta, $nazivUsluge)
{
    $pregledi = Pregled::join('usluga', 'pregled.idUsluga', '=', 'usluga.idUsluga')
        ->where('usluga.nazivUsluga', $nazivUsluge)
        ->where('pregled.idKorisnikPacijent', $idPacijenta)
        ->join('termin','termin.idTermin','=','pregled.idTermin')
        ->where(function ($query) {
            $query->where('pregled.obavljen', 0)
            ->where(function ($query) {
            $query->where('termin.datumTermina', '>', now()) // Samo termini u budućnosti
                ->orWhere(function ($query) {
                    $query->where('termin.datumTermina', now())
                          ->where('termin.vremeTermina', '>=', now());
                });
                });
        })
        ->join('korisnik', 'korisnik.idKorisnik', '=', 'pregled.idKorisnikDoktor')
        ->select(
            'korisnik.ime',
            'korisnik.prezime',
            'termin.datumTermina',
            'termin.vremeTermina',
            'usluga.nazivUsluga',
            'pregled.idPregled'
        )
        ->orderBy('termin.datumTermina', 'asc')
        ->orderBy('termin.vremeTermina', 'asc')
        ->get();

    $brojPregleda = $pregledi->count();

    return [
        'brojPregleda' => $brojPregleda,
        'pregledi' => $pregledi,
    ];
}
public function otkaziPregled($idPregleda){
    $pregled = Pregled::where('idPregled', $idPregleda)->first();

    if ($pregled) {
        $pregled->idKorisnikPacijent = null;
        $pregled->idKorisnikDoktor = null;
        $pregled->save();

    
        $termin = Termin::where('termin.idTermin',$pregled->idTermin)->first();

        if ($termin) {
            $termin->zakazan = 0;
            $termin->save();
        }

        return response()->json(['message' => 'Pregled je uspešno otkazan']);
    }

    // Ako pregled nije pronađen, vratite odgovarajuću poruku o grešci
    return response()->json(['message' => 'Pregled nije pronađen'], 404);
}

}