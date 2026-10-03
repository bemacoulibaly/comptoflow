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
        DB::unprepared("CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_valider_ecriture`(
    IN  p_ecriture_id BIGINT,
    OUT p_ok          TINYINT,
    OUT p_message     VARCHAR(255)
)
proc_valider: BEGIN
    DECLARE v_debit   DECIMAL(15,2) DEFAULT 0;
    DECLARE v_credit  DECIMAL(15,2) DEFAULT 0;
    DECLARE v_statut  VARCHAR(20)   DEFAULT NULL;
    DECLARE v_nb      INT           DEFAULT 0;

    SELECT statut INTO v_statut
    FROM ecritures
    WHERE id = p_ecriture_id
    LIMIT 1;

    IF v_statut IS NULL THEN
        SET p_ok = 0;
        SET p_message = 'Écriture introuvable.';
        LEAVE proc_valider;
    END IF;

    IF v_statut != 'brouillon' THEN
        SET p_ok = 0;
        SET p_message = CONCAT('Impossible : statut actuel = ', v_statut, '. Seules les écritures en brouillon peuvent être validées.');
        LEAVE proc_valider;
    END IF;

    SELECT COUNT(*) INTO v_nb
    FROM lignes_ecriture
    WHERE ecriture_id = p_ecriture_id;

    IF v_nb < 2 THEN
        SET p_ok = 0;
        SET p_message = CONCAT('Nombre de lignes insuffisant : ', v_nb, '. Minimum requis : 2.');
        LEAVE proc_valider;
    END IF;

    SELECT
        COALESCE(SUM(debit),  0),
        COALESCE(SUM(credit), 0)
    INTO v_debit, v_credit
    FROM lignes_ecriture
    WHERE ecriture_id = p_ecriture_id;

    IF ABS(v_debit - v_credit) > 0.01 THEN
        SET p_ok = 0;
        SET p_message = CONCAT(
            'Écriture déséquilibrée : débit = ', FORMAT(v_debit, 2),
            ' ≠ crédit = ', FORMAT(v_credit, 2),
            ' (écart = ', FORMAT(ABS(v_debit - v_credit), 2), ')'
        );
        LEAVE proc_valider;
    END IF;

    UPDATE ecritures
    SET statut     = 'validee',
        validee_at = NOW(),
        updated_at = NOW()
    WHERE id = p_ecriture_id;

    SET p_ok      = 1;
    SET p_message = 'Écriture validée avec succès.';
END proc_valider");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_valider_ecriture");
    }
};
