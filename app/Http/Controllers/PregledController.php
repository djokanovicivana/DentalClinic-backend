<?php
namespace App\Http\Controllers;
use Exception;
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

use Exception;

public function zakaziPregled($idTermin, $idKorisnikDoktor, $idKorisnikPacijent, $nazivUsluga){
    try {
        $pregled = new Pregled();
        $idUsluga = Usluga::where('nazivUsluga', $nazivUsluga)->firstOrFail();
        
        $pregled->id_termin = $idTermin;
        $pregled->id_korisnik_doktor = $idKorisnikDoktor;
        $pregled->id_korisnik_pacijent = $idKorisnikPacijent;
        $pregled->id_usluga = $idUsluga->id; // Koristite "id" iznad za atribut "id_usluga"
        $pregled->obavljen = 0; 
       
        $pregled->save();
        
        // Označavanje termina kao zakazanog
        Termin::where('idTermin', $idTermin)->update(['zakazan' => 1]);

        // Vratite odgovor o uspehu ako je sve u redu
        return response()->json(['success' => true, 'message' => 'Pregled je uspešno zakazan.']);
    } catch (Exception $e) {
        // Uhvatite izuzetak i vratite odgovor o grešci
        return response()->json(['success' => false, 'message' => 'Došlo je do greške prilikom zakazivanja pregleda.', 'error' => $e->getMessage()]);
    }
}

}