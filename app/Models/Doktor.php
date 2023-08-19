<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doktor extends Model
{
    protected $table = 'doktor';
    protected $primaryKey = 'idKorisnik';
    protected $fillable=['slika'];
    public $timestamps = false; // Ukoliko ne želite timestamp kolone

    // Primer relacije sa korisnikom
    public function korisnik()
    {
        return $this->belongsTo(Korisnik::class, 'idKorisnik', 'idKorisnik');
    }

    // Primer relacije sa granom
    public function grana()
    {
        return $this->belongsTo(Grana::class, 'idGrana', 'idGrana');
    }
}