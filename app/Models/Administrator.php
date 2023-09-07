<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
     use HasFactory;

    protected $table = 'administrator'; 

    protected $primaryKey = 'idKorisnik'; 

    public $timestamps = false; 
    public function korisnik()
    
    {
        return $this->belongsTo('App\Models\Korisnik', 'idKorisnik');
    }
}