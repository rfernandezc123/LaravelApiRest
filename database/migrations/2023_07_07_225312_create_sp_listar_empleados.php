<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = " CREATE PROCEDURE sp_list_employees()
        BEGIN
            SELECT * FROM empleados;
        END
        ";
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_list_employees");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_list_employees");
    }
};
