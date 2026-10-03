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
        DB::unprepared("CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_gen_numero_piece`(
    IN  p_societe_id BIGINT,
    IN  p_journal    VARCHAR(5),
    OUT p_numero     VARCHAR(50)
)
BEGIN
    DECLARE v_count INT DEFAULT 0;
    DECLARE v_annee INT DEFAULT YEAR(CURDATE());

    SELECT COUNT(*) + 1
    INTO   v_count
    FROM   ecritures
    WHERE  societe_id = p_societe_id
      AND  journal    = p_journal
      AND  YEAR(date_ecriture) = v_annee;

    SET p_numero = CONCAT(p_journal, '-', v_annee, '-', LPAD(v_count, 4, '0'));
END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_gen_numero_piece");
    }
};
