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
        $procedure = "CREATE PROCEDURE sp_update_empleados_pagos
        (
            IN _idEmpleado BIGINT,
            IN _nombre VARCHAR(100),
            IN _apellidos VARCHAR(200),
            IN _direccion VARCHAR(250),
            IN _telefono  VARCHAR(20),
            IN _pagos JSON,
            IN _pagosDestroy JSON
        )
        BEGIN
            DECLARE _idPago BIGINT;
            DECLARE _fecha  DATE;
            DECLARE _transaccion VARCHAR(250);
            DECLARE _importe DECIMAL(18,2);

            UPDATE empleados
                SET nombre = _nombre,
                    apellidos = _apellidos,
                    direccion = _direccion,
                    telefono = _telefono
                WHERE id = _idEmpleado;

            WHILE JSON_LENGTH(_pagos) > 0 DO
                SET _idPago = JSON_UNQUOTE(JSON_EXTRACT(_pagos,'$[0].id'));
                SET _fecha = JSON_UNQUOTE(JSON_EXTRACT(_pagos,'$[0].fecha'));
                SET _transaccion = JSON_UNQUOTE(JSON_EXTRACT(_pagos,'$[0].transaccion'));
                SET _importe = JSON_UNQUOTE(JSON_EXTRACT(_pagos,'$[0].importe'));

                IF _idPago > 0 THEN
                    UPDATE pagos
                        SET fecha = _fecha,
                            transaccion = _transaccion,
                            importe = _importe
                    WHERE id = _idPago;
                ELSE
                    INSERT INTO pagos(employee_id,fecha,transaccion,importe) VALUES (_idEmpleado, _fecha, _transaccion, _importe);
                END IF;

                SET _pagos = JSON_REMOVE(_pagos, '$[0]');

            END WHILE;

            WHILE JSON_LENGTH(_pagosDestroy) > 0 DO
                SET _idPago = JSON_UNQUOTE(JSON_EXTRACT(_pagosDestroy,'$[0].id'));

                DELETE FROM pagos WHERE id = _idPago;

                SET _pagosDestroy = JSON_REMOVE(_pagosDestroy, '$[0]');
            END WHILE;

        END
        ";
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_update_empleados_pagos");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_update_empleados_pagos");
    }
};
