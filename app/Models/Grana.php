<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grana extends Model
{
    use HasFactory;
       protected $table = 'grana'; 

       protected $primaryKey = 'idGrana'; 

       protected $fillable = ['nazivGrana']; 

       public $timestamps = false;
}