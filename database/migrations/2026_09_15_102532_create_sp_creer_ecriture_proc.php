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
        DB::unprepared("CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_creer_ecriture`(
    IN  p_societe_id     BIGINT,
    IN  p_user_id        BIGINT,
    IN  p_numero_piece   VARCHAR(50),
    IN  p_date_ecriture  DATE,
    IN  p_journal        VARCHAR(5),
    IN  p_libelle        VARCHAR(255),
    OUT p_ecriture_id    BIGINT
)
BEGIN
    INSERT INTO ecritures (
        societe_id, user_id, numero_piece,
        date_ecriture, journal, libelle, statut,
        created_at, updated_at
    ) VALUES (
        p_societe_id, p_user_id, p_numero_piece,
        p_date_ecriture, p_journal, p_libelle, 'brouillon',
        NOW(), NOW()
    );
    SET p_ecriture_id = LAST_INSERT_ID();
END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_creer_ecriture");
    }
};
