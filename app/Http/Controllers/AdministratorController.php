<?php

namespace App\Http\Controllers;
use App\Models\Administrator;
use App\Models\Korisnik;
use Illuminate\Http\Request;

class AdministratorController extends Controller
{
     public function adminId($adminId){
        $admin=Administrator::join('korisnik','korisnik.idKorisnik','=','administrator.idKorisnik')
        ->where('administrator.idKorisnik', $adminId)
        ->select('korisnik.*')
        ->get();

        return response()->json($admin);
    }
}