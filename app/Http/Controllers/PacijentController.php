<?php

namespace App\Http\Controllers;
use App\Models\Pacijent;
use App\Models\Korisnik;
use App\Models\Pregled;
use Illuminate\Http\Request;

class PacijentController extends Controller
{
    public function pacijentId($pacijentId){
        $pacijent=Pacijent::join('korisnik','korisnik.idKorisnik','=','pacijent.idKorisnik')
        ->where('pacijent.idKorisnik', $pacijentId)
        ->select('pacijent.brojKartona','korisnik.*')
        ->get();

        return response()->json($pacijent);
    }
   public function pacijentiDoktor(Request $request, $doktorId)
    {
        $searchTerm = $request->input('searchTerm');

        $query = Pacijent::join('pregled', 'pacijent.idKorisnik', '=', 'pregled.idKorisnikPacijent')
            ->join('korisnik', 'pacijent.idKorisnik', '=', 'korisnik.idKorisnik')
            ->where('pregled.idKorisnikDoktor', $doktorId)
            ->select('pacijent.brojKartona', 'korisnik.*')
            ->distinct();

        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('korisnik.ime', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('korisnik.prezime', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $pacijenti = $query->get();

        return response()->json($pacijenti);
    }
     public function sviPacijenti(Request $request){
        $searchTerm = $request->input('searchTerm');
        $query = Pacijent::join('korisnik', 'pacijent.idKorisnik', '=', 'korisnik.idKorisnik')
            ->select('pacijent.brojKartona', 'korisnik.*')
            ->distinct();

        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('korisnik.ime', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('korisnik.prezime', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $pacijenti = $query->get();

        return response()->json($pacijenti);
     }
}