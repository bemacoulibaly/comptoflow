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
        DB::unprepared("CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_calculer_tva`(
    IN p_societe_id    BIGINT,
    IN p_periode_debut DATE,
    IN p_periode_fin   DATE
)
BEGIN
    DECLARE v_collectee  DECIMAL(15,2) DEFAULT 0;
    DECLARE v_deductible DECIMAL(15,2) DEFAULT 0;

    SELECT COALESCE(SUM(montant_tva), 0)
    INTO   v_collectee
    FROM   factures
    WHERE  societe_id    = p_societe_id
      AND  type          = 'client'
      AND  date_emission BETWEEN p_periode_debut AND p_periode_fin
      AND  statut NOT IN ('brouillon', 'annulee');

    SELECT COALESCE(SUM(montant_tva), 0)
    INTO   v_deductible
    FROM   factures
    WHERE  societe_id    = p_societe_id
      AND  type          = 'fournisseur'
      AND  date_emission BETWEEN p_periode_debut AND p_periode_fin
      AND  statut NOT IN ('brouillon', 'annulee');

    SELECT
        p_periode_debut                          AS periode_debut,
        p_periode_fin                            AS periode_fin,
        ROUND(v_collectee,  2)                   AS tva_collectee,
        ROUND(v_deductible, 2)                   AS tva_deductible,
        ROUND(v_collectee - v_deductible, 2)     AS tva_nette_a_reverser,
        DATE_ADD(p_periode_fin, INTERVAL 15 DAY) AS date_echeance_paiement;
END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_calculer_tva");
    }
};
