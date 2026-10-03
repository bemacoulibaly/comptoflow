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
        DB::statement("CREATE VIEW `vue_balance` AS select `c`.`numero` AS `numero`,`c`.`libelle` AS `libelle`,`c`.`classe` AS `classe`,`c`.`type` AS `type`,coalesce(sum(`l`.`debit`),0) AS `total_debit`,coalesce(sum(`l`.`credit`),0) AS `total_credit`,coalesce(sum(`l`.`debit`),0) - coalesce(sum(`l`.`credit`),0) AS `solde` from ((`comptoflow`.`comptes` `c` left join `comptoflow`.`lignes_ecriture` `l` on(`l`.`compte_id` = `c`.`id`)) left join `comptoflow`.`ecritures` `e` on(`e`.`id` = `l`.`ecriture_id` and `e`.`statut` = 'validee')) group by `c`.`id`,`c`.`numero`,`c`.`libelle`,`c`.`classe`,`c`.`type` order by `c`.`numero`");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `vue_balance`");
    }
};
