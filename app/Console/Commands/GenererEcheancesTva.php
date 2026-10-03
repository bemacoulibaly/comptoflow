<?php

namespace App\Console\Commands;

use App\Models\Evenement;
use App\Models\Societe;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenererEcheancesTva extends Command
{
    protected $signature   = 'tva:generer-echeances {--mois= : Mois cible (1-12)}';
    protected $description = 'Générer automatiquement les échéances TVA dans le calendrier';

    public function handle(): int
    {
        $mois   = (int) ($this->option('mois') ?? now()->month);
        $annee  = now()->year;
        $debut  = Carbon::createFromDate($annee, $mois, 1);
        $fin    = $debut->copy()->endOfMonth();
        $echeance = $fin->copy()->addDays(15);

        Societe::all()->each(function (Societe $societe) use ($debut, $echeance) {
            $titre = "Déclaration TVA — " . $debut->translatedFormat('F Y');

            $exists = Evenement::where('societe_id', $societe->id)
                ->where('type', 'tva')
                ->where('date_echeance', $echeance->toDateString())
                ->exists();

            if (!$exists) {
                Evenement::create([
                    'societe_id'    => $societe->id,
                    'user_id'       => $societe->users()->where('role', 'admin')->first()?->id ?? 1,
                    'titre'         => $titre,
                    'description'   => "TVA nette à calculer et reverser avant le {$echeance->format('d/m/Y')}.",
                    'date_echeance' => $echeance->toDateString(),
                    'type'          => 'tva',
                    'priorite'      => 'haute',
                ]);
                $this->info("  → {$societe->raison_sociale} : échéance TVA ajoutée ({$echeance->format('d/m/Y')})");
            }
        });

        $this->info("✅ Échéances TVA générées.");
        return Command::SUCCESS;
    }
}
