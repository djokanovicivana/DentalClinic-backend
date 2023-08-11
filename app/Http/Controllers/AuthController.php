<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Korisnik;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'korisnickoIme' => 'required',
                'password' => 'required'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validaciona greška',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $credentials = [
                'korisnickoIme' => $request->korisnickoIme,
                'password' => $request->password
            ];

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pogrešno korisničko ime ili lozinka.',
                ], 401);
            }

            $korisnik = Korisnik::where('korisnickoIme', $request->korisnickoIme)->first();

            return response()->json([
                'status' => true,
                'message' => 'Uspešno ste se prijavili.',
                'token' => $korisnik->createToken("API TOKEN")->plainTextToken,
                'korisnikId'=>$korisnik->idKorisnik,
                'korisnikUloga'=>$korisnik->uloga
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}