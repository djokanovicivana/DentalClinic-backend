<?php
namespace App\Http\Controllers;
use App\Models\Grana;
use Illuminate\Http\Request;


class GranaController extends Controller
{
    public function index(){
      $grane=Grana::all(); 
      return response()->json($grane);
        
    }
}