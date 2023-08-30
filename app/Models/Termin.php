<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Termin extends Model
{
    use HasFactory;
    protected $table = 'termin'; 

    protected $primaryKey = 'idTermin'; 

    protected $fillable = ['datumTermina', 'vremeTermina', 'prostorija', 'zakazan', 'idKorisnik']; 
    public $timestamps = false;

    public function doktor()
    {
        return $this->belongsTo(Doktor::class, 'idKorisnik', 'idKorisnik');
    }
}