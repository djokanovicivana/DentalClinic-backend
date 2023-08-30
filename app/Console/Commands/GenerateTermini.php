<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Doktor;
use App\Models\Termin;

class GenerateTermini extends Command
{
    protected $signature = 'generate:termini';
    protected $description = 'Generiše termine za sve doktore';

    public function handle()
    {
        // Prolazi kroz sve doktore
        $doktori = Doktor::all();
        foreach ($doktori as $doktor) {
            $this->info("Generisanje termina za doktora: {$doktor->idKorisnik}");

            // Postavlja datum na trenutni datum
            $datum = Carbon::now();

            // Prolazi kroz narednih 30 dana
            for ($i = 0; $i < 30; $i++) {
                $currentDate = $datum->clone(); // Klonira instancu da bi se zadržao originalni datum
                // Prolazi kroz termine od 9 do 17 sa intervalom od 30 minuta
                $vreme = Carbon::createFromTime(9, 0);
                while ($vreme->hour < 17) {
                    // Kreira novi termin
                    Termin::create([
                        'datumTermina' => $currentDate->format('Y-m-d'),
                        'vremeTermina' => $vreme->format('H:i:s'),
                        'zakazan' => false,
                        'idKorisnik' => $doktor->idKorisnik,
                    ]);

                    // Dodaje 30 minuta na vreme
                    $vreme->addMinutes(30);
                }

                // Dodaje 1 dan na datum
                $datum->addDay();
            }

            $this->info("Termini generisani za doktora: {$doktor->idKorisnik}");
        }

        $this->info("Generisanje termina završeno!");
        return Command::SUCCESS;
    }
}