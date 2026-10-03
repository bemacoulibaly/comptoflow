<?php

namespace App\Console\Commands;

use App\Models\Facture;
use App\Models\Societe;
use App\Services\FactureService;
use Illuminate\Console\Command;

class VerifierFacturesRetard extends Command
{
    protected $signature   = 'factures:verifier-retards';
    protected $description = 'Marquer les factures dépassant leur échéance comme "en retard"';

    public function handle(FactureService $service): int
    {
        $total = 0;

        Societe::all()->each(function (Societe $societe) use ($service, &$total) {
            $nb = $service->verifierRetards($societe);
            if ($nb > 0) {
                $this->info("  → {$societe->raison_sociale} : {$nb} facture(s) passée(s) en retard.");
                $total += $nb;
            }
        });

        $this->info("✅ Vérification terminée — {$total} facture(s) mise(s) à jour.");
        return Command::SUCCESS;
    }
}
