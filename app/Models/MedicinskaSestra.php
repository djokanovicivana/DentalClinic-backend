<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Doktor;
use App\Models\Korisnik;

class MedicinskaSestra extends Model
{
    use HasFactory;
    protected $table = 'medicinskaSestra';
    protected $primaryKey = 'idKorisnik';
    public $timestamps = false; 

    
    public function korisnik()
    {
        return $this->belongsTo(Korisnik::class, 'idKorisnik', 'idKorisnik');
    }
     public function doktor()
    {
        return $this->hasOne(Doktor::class, 'idDoktor');
    }
}