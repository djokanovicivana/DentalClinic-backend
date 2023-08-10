<?php

namespace App\Http\Controllers;
use App\Models\Korisnik;
use App\Models\Pacijent;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class KorisnikController extends Controller
{
   public function create(Request $request)
{
    // Validacija unetih podataka
    $request->validate([
        'korisnickoIme' => 'required|unique:korisnik,korisnickoIme',
        'password' => 'required|min:6|confirmed',
        'password_confirmation' => 'required',
        'email' => 'required|email|unique:korisnik,email',
        'ime' => 'required',
        'prezime' => 'required',
        'brojTelefona' => 'required|min:9',
        
    ]);

    // Kreiranje novog korisnika (pacijenta)
    $korisnik = new Korisnik();
    $korisnik->korisnickoIme = $request->korisnickoIme;
    $korisnik->password = Hash::make($request->password);
    $korisnik->email = $request->email;
    $korisnik->ime = $request->ime;
    $korisnik->prezime = $request->prezime;
    $korisnik->brojTelefona = $request->brojTelefona;
    $korisnik->uloga = 'Pacijent'; // Postavite ulogu na "Pacijent"
    // Dodajte ostale atribute pacijenta

    // Sačuvajte korisnika/pacijenta
    $korisnik->save();

    // Kreiranje novog pacijenta
    $pacijent = new Pacijent();
    $pacijent->idKorisnik = $korisnik->idKorisnik; // Povezivanje sa korisnikom
    // Dodajte ostale atribute pacijenta

    // Sačuvajte pacijenta
    $pacijent->save();

    return response()->json([
        'status' => true,
        'message' => 'Pacijent uspešno registrovan kao korisnik i pacijent.',
        'user' => $korisnik,
        'pacijent' => $pacijent,
    ], 201);
}

}