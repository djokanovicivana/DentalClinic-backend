<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
     public function login(Request $request)
{
    try {
        $validateUser = Validator::make($request->all(), [
            'korisnickoIme' => 'required',
            'lozinka' => 'required'
        ]);

        $customAttributes = [
            'korisnickoIme' => 'Korisničko ime',
            'lozinka' => 'Lozinka',
        ];

        $validateUser->setAttributeNames($customAttributes);

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Greška pri validaciji',
                'errors' => $validateUser->errors()
            ], 401);
        }

        if (!Auth::attempt($request->only(['korisnickoIme', 'lozinka']))) {
            return response()->json([
                'status' => false,
                'message' => 'Korisničko ime ili lozinka su pogrešni.',
            ], 401);
        }

        $korisnik = Korisnik::where('korisnickoIme', $request->korisnickoIme)->first();

        return response()->json([
            'status' => true,
            'message' => 'Korisnik uspešno prijavljen',
            'token' => $korisnik->createToken("API TOKEN")->plainTextToken
        ], 200);

    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
}
}