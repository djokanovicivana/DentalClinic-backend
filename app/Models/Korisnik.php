<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Korisnik extends Authenticatable implements AuthenticatableContract
{
    use HasFactory;
      protected $table = 'korisnik'; // Ime tabele u bazi podataka

    protected $primaryKey = 'idKorisnik'; // Primarni ključ tabele

    protected $fillable = [
        'korisnickoIme',
        'lozinka',
        'ime',
        'prezime',
        'brojTelefona',
        'email',
        'uloga',
    ];

    public function doktor()
    {
        return $this->hasOne('App\Doktor', 'idKorisnik');
    }

    public function administrator()
    {
        return $this->hasOne('App\Administrator', 'idKorisnik');
    }

    public function medicinskaSestra()
    {
        return $this->hasOne('App\MedicinskaSestra', 'idKorisnik');
    }

    public function pacijent()
    {
        return $this->hasOne('App\Pacijent', 'idKorisnik');
    }
}