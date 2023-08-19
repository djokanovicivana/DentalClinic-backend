<?php
namespace App\Http\Controllers;
use App\Models\Pacijent;
use App\Models\Pregled;
use App\Models\Korisnik;
use Illuminate\Http\Request;



class PregledController extends Controller
{
    public function pacijentiDoktor($doktorId)
    {
        $pacijenti = Pacijent::join('pregled', 'pacijent.idKorisnik', '=', 'pregled.idKorisnikPacijent')
            ->join('korisnik', 'pacijent.idKorisnik', '=', 'korisnik.idKorisnik')
            ->where('pregled.idKorisnikDoktor', $doktorId)
            ->select('pacijent.brojKartona', 'korisnik.*')
            ->get();

        return response()->json($pacijenti);
    }
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
    $pregled->idUsluga=$request->input('idUsluga');
    $pregled->anamneza = $request->input('anamneza');
    $pregled->dijagnoza = $request->input('dijagnoza');
    $pregled->lecenje = $request->input('lecenje');
    $pregled->obavljen = 1; 

    $pregled->save();

    return response()->json(['message' => 'Pregled je uspešno ažuriran'], 200);
}

    
}