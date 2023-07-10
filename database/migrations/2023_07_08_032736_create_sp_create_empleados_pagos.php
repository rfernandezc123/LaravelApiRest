<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "CREATE PROCEDURE sp_create_empleados_pagos
        (
            IN _nombre varchar(100),
            IN _apellidos varchar(200),
            IN _direccion varchar(250),
            IN _telefono varchar(20),
            IN _pagos JSON
        )
        BEGIN
            DECLARE _empleado_id BIGINT;
            DECLARE _fecha DATE;
            DECLARE _transaccion varchar(250);
            DECLARE _importe DECIMAL(18,2);

            INSERT INTO empleados(nombre,apellidos,direccion,telefono) VALUES (_nombre, _apellidos, _direccion, _telefono);

            SET _empleado_id = LAST_INSERT_ID();

            WHILE JSON_LENGTH(_pagos) > 0 DO
            -- JSON_UNQUOTE: elimina las comillas del json
            -- JSON_EXTRACT: extraemos un valor en especifico del json
                SET _fecha = JSON_UNQUOTE(JSON_EXTRACT(_pagos,'$[0].fecha'));
                SET _transaccion = JSON_UNQUOTE(JSON_EXTRACT(_pagos,'$[0].transaccion'));
                SET _importe = JSON_UNQUOTE(JSON_EXTRACT(_pagos, '$[0].importe'));

                INSERT INTO pagos(employee_id,fecha,transaccion,importe) VALUES (_empleado_id,_fecha, _transaccion, _importe);

                SET _pagos = JSON_REMOVE(_pagos, '$[0]');

            END WHILE;
        END;
        ";
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_create_empleados_pagos');
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_create_empleados_pagos');
    }
};
