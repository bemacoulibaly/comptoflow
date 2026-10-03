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
        DB::statement("CREATE VIEW `vue_factures_en_retard` AS select `f`.`numero` AS `numero`,`t`.`nom` AS `client`,`f`.`montant_ttc` AS `montant_ttc`,`f`.`montant_paye` AS `montant_paye`,`f`.`montant_ttc` - `f`.`montant_paye` AS `solde_restant`,`f`.`date_echeance` AS `date_echeance`,to_days(curdate()) - to_days(`f`.`date_echeance`) AS `jours_retard` from (`comptoflow`.`factures` `f` join `comptoflow`.`tiers` `t` on(`t`.`id` = `f`.`tiers_id`)) where `f`.`type` = 'client' and `f`.`statut` in ('en_retard','emise','partielle') and `f`.`date_echeance` < curdate() order by `f`.`date_echeance`");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `vue_factures_en_retard`");
    }
};
