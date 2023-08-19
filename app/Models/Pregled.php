<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pregled extends Model
{
    protected $table = 'pregled';
    protected $primaryKey = 'idPregled';
    public $timestamps = false; // Ako tabela nema created_at i updated_at kolone

    protected $fillable = [
        'idTermin',
        'idKorisnikPacijent',
        'idKorisnikDoktor',
        'idUsluga',
        'anamneza',
        'dijagnoza',
        'lecenje',
        'obavljen',
        'zakazan',
    ];

    public function pacijent()
    {
        return $this->belongsTo(Korisnik::class, 'idKorisnikPacijent');
    }

    public function doktor()
    {
        return $this->belongsTo(Korisnik::class, 'idKorisnikDoktor');
    }
}