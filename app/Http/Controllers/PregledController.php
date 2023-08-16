<?php
namespace App\Http\Controllers;
use App\Models\Pacijent;
use App\Models\Pregled;
use App\Models\Korisnik;



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
}