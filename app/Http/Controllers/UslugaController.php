<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usluga;

class UslugaController extends Controller
{
    public function index($id){
        $usluge=Usluga::where('idGrana',$id)->get();
    return response()->json($usluge);
    }
    public function show($id){
        $usluga=Usluga::where('idUsluga',$id)->get();
        return response()->json($usluga);
    }
}