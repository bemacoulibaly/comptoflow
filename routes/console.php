<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Vérifier les factures en retard chaque jour à minuit
Schedule::command('factures:verifier-retards')->daily();

// Sauvegarde automatique chaque nuit à 2h00, conserve 30 jours
Schedule::command('comptoflow:sauvegarder --garder=30')->dailyAt('02:00');

// Générer les échéances TVA du prochain mois (le 25 du mois courant)
Schedule::command('tva:generer-echeances')->monthlyOn(25, '08:00');
