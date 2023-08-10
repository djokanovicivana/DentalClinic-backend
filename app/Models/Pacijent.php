<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pacijent extends Model
{
    use HasFactory;

    protected $table = 'pacijent'; // Ime tabele u bazi podataka

    protected $primaryKey = 'idKorisnik'; // Primarni ključ tabele

    public $timestamps = false; 

    public function korisnik()
    {
        return $this->belongsTo('App\Models\Korisnik', 'idKorisnik');
    }
}