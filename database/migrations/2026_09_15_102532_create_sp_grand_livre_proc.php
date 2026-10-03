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
        DB::unprepared("CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_grand_livre`(
    IN p_societe_id BIGINT,
    IN p_numero     VARCHAR(20),
    IN p_debut      DATE,
    IN p_fin        DATE
)
BEGIN
    DECLARE v_compte_id BIGINT  DEFAULT NULL;
    DECLARE v_type      VARCHAR(20);

    SELECT id, type
    INTO   v_compte_id, v_type
    FROM   comptes
    WHERE  societe_id = p_societe_id
      AND  numero     = p_numero
    LIMIT  1;

    IF v_compte_id IS NULL THEN
        SELECT CONCAT('Compte ', p_numero, ' introuvable pour cette société.') AS erreur;
    ELSE
        SELECT
            e.date_ecriture                                          AS date_operation,
            e.numero_piece                                           AS piece,
            e.journal,
            l.libelle                                                AS libelle_ligne,
            l.debit,
            l.credit,
            SUM(
                CASE
                    WHEN v_type IN ('actif', 'charge')
                        THEN l.debit - l.credit
                    ELSE
                        l.credit - l.debit
                END
            ) OVER (
                ORDER BY e.date_ecriture, e.id, l.ordre
                ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
            ) AS solde_cumule
        FROM   lignes_ecriture l
        JOIN   ecritures e ON e.id = l.ecriture_id
        WHERE  l.compte_id      = v_compte_id
          AND  e.statut         = 'validee'
          AND  e.date_ecriture  BETWEEN p_debut AND p_fin
        ORDER BY e.date_ecriture, e.id, l.ordre;
    END IF;
END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_grand_livre");
    }
};
