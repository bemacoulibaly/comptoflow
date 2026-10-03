<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE VIEW `vue_journal` AS select `e`.`date_ecriture` AS `date_ecriture`,`e`.`numero_piece` AS `numero_piece`,`e`.`journal` AS `journal`,`e`.`libelle` AS `libelle_ecriture`,`c`.`numero` AS `compte`,`c`.`libelle` AS `libelle_compte`,`l`.`libelle` AS `libelle_ligne`,`l`.`debit` AS `debit`,`l`.`credit` AS `credit`,`e`.`statut` AS `statut` from ((`comptoflow`.`ecritures` `e` join `comptoflow`.`lignes_ecriture` `l` on(`l`.`ecriture_id` = `e`.`id`)) join `comptoflow`.`comptes` `c` on(`c`.`id` = `l`.`compte_id`)) order by `e`.`date_ecriture`,`e`.`numero_piece`,`l`.`ordre`");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `vue_journal`");
    }
};
