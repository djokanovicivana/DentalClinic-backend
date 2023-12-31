<?php

namespace App\Http\Controllers;
use App\Models\Korisnik;
use App\Models\Pacijent;
use App\Models\Doktor;
use App\Models\Administrator;
use App\Models\MedicinskaSestra;
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
public function updateKorisnik(Request $request, $korisnickoIme) {
    $korisnik = Korisnik::where('korisnickoIme', $korisnickoIme)->first();

    if (!$korisnik) {
        return response()->json(['message' => 'Korisnik nije pronađen'], 404);
    }

    $dataToUpdate = [];

    if ($request->has('ime')) {
        $dataToUpdate['ime'] = $request->ime;
    }

    if ($request->has('prezime')) {
        $dataToUpdate['prezime'] = $request->prezime;
    }

    if ($request->has('brojTelefona')) {
        $dataToUpdate['brojTelefona'] = $request->brojTelefona;
    }

    if ($request->has('godiste')) {
        $dataToUpdate['godiste'] = $request->godiste;
    }

    if ($request->has('email')) {
        $request->validate([
            'email' => 'email|unique:korisnik,email,'. $korisnickoIme . ',korisnickoIme',
        ]);

        $dataToUpdate['email'] = $request->email;
    }

if ($request->has('old_password') && $request->has('new_password') && $request->has('new_password_confirmation')) {
    $korisnik = Korisnik::where('korisnickoIme', $korisnickoIme)->first();

    if (!$korisnik) {
        return response()->json(['message' => 'Korisnik nije pronađen'], 404);
    }

    // Provera da li unesena stara šifra odgovara trenutnoj šifri korisnika
    if (!Hash::check($request->old_password, $korisnik->password)) {
        return response()->json(['error' => 'Stara šifra nije tačna.'], 400);
    }

    $newPassword = $request->new_password;
    $newPasswordConfirmation = $request->new_password_confirmation;

    if ($newPassword !== $newPasswordConfirmation) {
        return response()->json(['error' => 'Nova šifra se ne poklapa sa potvrdom.'], 400);
    }

    $dataToUpdate['password'] = Hash::make($newPassword);
}

    if (!empty($dataToUpdate)) {
        $korisnik->update($dataToUpdate);
    }

    return response()->json(['message' => 'Podaci korisnika su uspešno ažurirani'], 200);
}



public function updateDoktor(Request $request, $korisnickoIme) {
    $doktor = Korisnik::where('korisnickoIme', $korisnickoIme)
                      ->where('uloga', 'Doktor')
                      ->first();

    if (!$doktor) {
        return response()->json(['error' => 'Doktor nije pronađen.'], 404);
    }

    $dataToUpdate = [];

    // Provera i ažuriranje imena
    if ($request->has('ime')) {
        $dataToUpdate['ime'] = $request->ime;
    }

    // Provera i ažuriranje prezimena
    if ($request->has('prezime')) {
        $dataToUpdate['prezime'] = $request->prezime;
    }

    // Provera i ažuriranje broja telefona
    if ($request->has('brojTelefona')) {
        $dataToUpdate['brojTelefona'] = $request->brojTelefona;
    }
       if ($request->has('slika')) {
        $dataToUpdate['slika'] = $request->slika;
    }

    // Provera i ažuriranje godišta
    if ($request->has('godiste')) {
        $dataToUpdate['godiste'] = $request->godiste;
    }

    // Provera i ažuriranje korisničkog imena
    if ($request->has('korisnickoIme')) {
        // Proverite jedinstvenost korisničkog imena osim za trenutnog doktora
        $request->validate([
            'korisnickoIme' => 'unique:korisnik,korisnickoIme,' . $doktor->idKorisnik . ',idKorisnik',
        ]);

        $dataToUpdate['korisnickoIme'] = $request->korisnickoIme;
    }

    // Provera i ažuriranje emaila
    if ($request->has('email')) {
        // Proverite jedinstvenost emaila osim za trenutnog doktora
        $request->validate([
            'email' => 'email|unique:korisnik,email,' . $doktor->idKorisnik . ',idKorisnik',
        ]);

        $dataToUpdate['email'] = $request->email;
    }

    // Provera i ažuriranje šifre
    if ($request->has('old_password') && $request->has('new_password') && $request->has('new_password_confirmation')) {
        // Provera starih i novih šifri, ažuriranje ako je sve u redu
        // ...

        $dataToUpdate['password'] = Hash::make($newPassword);
    }

    // Ažuriranje idGrane na osnovu naziva grane
    if ($request->has('nazivGrana')) {
        $nazivGrane = $request->nazivGrana;
        $grana = Grana::where('nazivGrana', $nazivGrana)->first();

        if ($grana) {
            $dataToUpdate['idGrana'] = $grana->idGrane;
        } else {
            return response()->json(['error' => 'Grana nije pronađena.'], 404);
        }
    }

    if (!empty($dataToUpdate)) {
        $doktor->update($dataToUpdate);
    }

    return response()->json(['success' => true]);
}
public function deleteKorisnik($uloga, $id) {
    // Provera tipa korisnika na osnovu uloge
    switch ($uloga) {
        case 'Administrator':
            $model = Administrator::find($id);
            break;
        case 'MedicinskaSestra':
            $model = MedicinskaSestra::find($id);
            break;
        case 'Doktor':
            $model = Doktor::find($id);
            break;
        case 'Pacijent':
            $model = Pacijent::find($id);
            break;
        
        default:
            return response()->json(['message' => 'Nepoznata uloga korisnika'], 400);
    }

    if (!$model) {
        return response()->json(['message' => 'Korisnik nije pronađen'], 404);
    }

    $model->delete();

    return response()->json(['message' => 'Korisnik je uspešno obrisan'], 200);
}







}