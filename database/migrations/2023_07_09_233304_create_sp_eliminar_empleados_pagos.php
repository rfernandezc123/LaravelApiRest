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
        $procedure = "CREATE PROCEDURE sp_eliminar_empleados_pagos
        (
            IN _idEmpleado BIGINT
        )
        BEGIN
            DELETE FROM empleados WHERE id = _idEmpleado;
        END
        ";
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_eliminar_empleados_pagos");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_eliminar_empleados_pagos");
    }
};
