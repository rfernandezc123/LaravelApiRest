<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
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
        $procedure = "CREATE PROCEDURE sp_get_empleados_pago
        (
            IN _idEmpleado BIGINT
        )
        BEGIN
            SELECT e.id,e.nombre,e.apellidos, e.direccion, e.telefono,
            JSON_ARRAYAGG(JSON_OBJECT('id', p.id, 'fecha', p.fecha, 'transaccion',p.transaccion,'importe',p.importe)) AS pagos
            FROM empleados AS e
            INNER JOIN pagos AS p
            ON e.id = p.employee_id
            WHERE e.id = _idEmpleado
            GROUP BY e.id;
        END
        ";
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_empleados_pago");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_empleados_pago");
    }
};
