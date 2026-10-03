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
        DB::unprepared("CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_paiement_facture`(
    IN  p_facture_id BIGINT,
    IN  p_montant    DECIMAL(15,2),
    OUT p_ok         TINYINT,
    OUT p_message    VARCHAR(255)
)
proc_paiement: BEGIN
    DECLARE v_ttc          DECIMAL(15,2) DEFAULT 0;
    DECLARE v_paye         DECIMAL(15,2) DEFAULT 0;
    DECLARE v_statut       VARCHAR(20)   DEFAULT NULL;
    DECLARE v_nouveau_paye DECIMAL(15,2) DEFAULT 0;
    DECLARE v_solde        DECIMAL(15,2) DEFAULT 0;

    SELECT montant_ttc, montant_paye, statut
    INTO   v_ttc, v_paye, v_statut
    FROM   factures
    WHERE  id = p_facture_id
    LIMIT  1;

    IF v_statut IS NULL THEN
        SET p_ok = 0;
        SET p_message = 'Facture introuvable.';
        LEAVE proc_paiement;
    END IF;

    IF v_statut IN ('payee', 'annulee') THEN
        SET p_ok = 0;
        SET p_message = CONCAT('Impossible : facture déjà ', v_statut, '.');
        LEAVE proc_paiement;
    END IF;

    IF p_montant <= 0 THEN
        SET p_ok = 0;
        SET p_message = 'Le montant doit être strictement supérieur à zéro.';
        LEAVE proc_paiement;
    END IF;

    IF p_montant > (v_ttc - v_paye) THEN
        SET p_ok = 0;
        SET p_message = CONCAT('Montant trop élevé. Solde restant : ', FORMAT(v_ttc - v_paye, 0), ' FCFA.');
        LEAVE proc_paiement;
    END IF;

    SET v_nouveau_paye = v_paye + p_montant;
    SET v_solde        = v_ttc - v_nouveau_paye;

    UPDATE factures
    SET montant_paye = v_nouveau_paye,
        statut       = CASE
                           WHEN v_nouveau_paye >= v_ttc THEN 'payee'
                           WHEN v_nouveau_paye  > 0     THEN 'partielle'
                           ELSE statut
                       END,
        updated_at   = NOW()
    WHERE id = p_facture_id;

    SET p_ok      = 1;
    SET p_message = CONCAT(
        'Paiement de ', FORMAT(p_montant, 0), ' FCFA enregistré. ',
        'Solde restant : ', FORMAT(GREATEST(0, v_solde), 0), ' FCFA.'
    );
END proc_paiement");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_paiement_facture");
    }
};
