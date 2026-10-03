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
        DB::unprepared("CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_bilan`(
    IN p_societe_id BIGINT,
    IN p_annee      INT
)
BEGIN
    DECLARE v_debut DATE DEFAULT DATE(CONCAT(p_annee, '-01-01'));
    DECLARE v_fin   DATE DEFAULT DATE(CONCAT(p_annee, '-12-31'));

    SELECT
        c.classe,
        c.type,
        c.numero,
        c.libelle,
        COALESCE(SUM(l.debit),  0)  AS total_debit,
        COALESCE(SUM(l.credit), 0)  AS total_credit,
        CASE
            WHEN c.type IN ('actif', 'charge')
                THEN COALESCE(SUM(l.debit),0)  - COALESCE(SUM(l.credit),0)
            ELSE
                COALESCE(SUM(l.credit),0) - COALESCE(SUM(l.debit),0)
        END AS solde
    FROM   comptes c
    LEFT JOIN lignes_ecriture l ON l.compte_id = c.id
    LEFT JOIN ecritures e ON e.id = l.ecriture_id
           AND e.statut        = 'validee'
           AND e.date_ecriture BETWEEN v_debut AND v_fin
    WHERE  c.societe_id = p_societe_id
    GROUP  BY c.id, c.classe, c.type, c.numero, c.libelle
    HAVING solde <> 0
    ORDER  BY c.classe, c.numero;
END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_bilan");
    }
};
