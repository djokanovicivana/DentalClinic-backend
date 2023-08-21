<?php

namespace App\Http\Controllers;
use App\Models\Pacijent;
use App\Models\Korisnik;
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
    
}