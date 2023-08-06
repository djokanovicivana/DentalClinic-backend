<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Termin extends Model
{
    use HasFactory;
    protected $table = 'termin'; 

    protected $primaryKey = 'idTermin'; 

    protected $fillable = ['datumTermina', 'vremeTermina', 'prostorija', 'zakazan']; 
    public $timestamps = false;

}