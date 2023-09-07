<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doktor extends Model
{
    protected $table = 'doktor';
    protected $primaryKey = 'idKorisnik';
    public $timestamps = false; 

    
    public function korisnik()
    {
        return $this->belongsTo(Korisnik::class, 'idKorisnik', 'idKorisnik');
    }


    public function grana()
    {
        return $this->belongsTo(Grana::class, 'idGrana', 'idGrana');
    }


    public function termini()
    {
        return $this->hasMany(Termin::class, 'idKorisnik', 'idKorisnik');
    }
}