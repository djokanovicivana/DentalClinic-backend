<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usluga extends Model
{
    use HasFactory;
     protected $table = 'usluga'; 

    protected $primaryKey = 'idUsluga'; 

    protected $fillable = ['nazivUsluga', 'opisUsluga', 'cenaUsluga', 'slikaUsluga', 'idGrana']; 


    public $timestamps = false;
    
    public function grana()
    {
        return $this->belongsTo(Grana::class, 'idGrana', 'idGrana');
    }
   
}