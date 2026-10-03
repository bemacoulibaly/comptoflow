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
        DB::unprepared("CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_dashboard_kpis`(
    IN p_societe_id BIGINT,
    IN p_debut      DATE,
    IN p_fin        DATE
)
BEGIN
    DECLARE v_recettes   DECIMAL(15,2) DEFAULT 0;
    DECLARE v_charges    DECIMAL(15,2) DEFAULT 0;
    DECLARE v_tresorerie DECIMAL(15,2) DEFAULT 0;
    DECLARE v_retards    INT           DEFAULT 0;
    DECLARE v_brouillons INT           DEFAULT 0;
    DECLARE v_tva_coll   DECIMAL(15,2) DEFAULT 0;
    DECLARE v_tva_ded    DECIMAL(15,2) DEFAULT 0;

    SELECT COALESCE(SUM(montant_ht), 0) INTO v_recettes
    FROM   factures
    WHERE  societe_id    = p_societe_id
      AND  type          = 'client'
      AND  date_emission BETWEEN p_debut AND p_fin
      AND  statut NOT IN ('brouillon', 'annulee');

    SELECT COALESCE(SUM(montant_ht), 0) INTO v_charges
    FROM   factures
    WHERE  societe_id    = p_societe_id
      AND  type          = 'fournisseur'
      AND  date_emission BETWEEN p_debut AND p_fin
      AND  statut NOT IN ('brouillon', 'annulee');

    SELECT COALESCE(SUM(l.debit) - SUM(l.credit), 0)
    INTO   v_tresorerie
    FROM   lignes_ecriture l
    JOIN   comptes c   ON c.id  = l.compte_id
                      AND c.classe     = '5'
                      AND c.type       = 'actif'
                      AND c.societe_id = p_societe_id
    JOIN   ecritures e ON e.id  = l.ecriture_id
                      AND e.statut = 'validee';

    SELECT COUNT(*) INTO v_retards
    FROM   factures
    WHERE  societe_id    = p_societe_id
      AND  type          = 'client'
      AND  statut NOT IN ('payee', 'annulee')
      AND  date_echeance < CURDATE();

    SELECT COUNT(*) INTO v_brouillons
    FROM   ecritures
    WHERE  societe_id = p_societe_id
      AND  statut     = 'brouillon';

    SELECT COALESCE(SUM(montant_tva), 0) INTO v_tva_coll
    FROM   factures
    WHERE  societe_id    = p_societe_id
      AND  type          = 'client'
      AND  date_emission BETWEEN p_debut AND p_fin
      AND  statut NOT IN ('brouillon', 'annulee');

    SELECT COALESCE(SUM(montant_tva), 0) INTO v_tva_ded
    FROM   factures
    WHERE  societe_id    = p_societe_id
      AND  type          = 'fournisseur'
      AND  date_emission BETWEEN p_debut AND p_fin
      AND  statut NOT IN ('brouillon', 'annulee');

    SELECT
        ROUND(v_recettes,    2)           AS recettes_ht,
        ROUND(v_charges,     2)           AS charges_ht,
        ROUND(v_recettes - v_charges, 2)  AS resultat_net,
        ROUND(v_tresorerie,  2)           AS tresorerie,
        v_retards                         AS factures_en_retard,
        v_brouillons                      AS ecritures_a_valider,
        ROUND(v_tva_coll - v_tva_ded, 2)  AS tva_nette_estimee;
END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_dashboard_kpis");
    }
};
