<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Empleados;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\EmpleadosRequest;

class EmpleadosController extends Controller
{
    public function index()
    {
        $employees = DB::select("CALL sp_list_employees()");
        return response()->json($employees, 200);
    }
    public function store(EmpleadosRequest $request)
    {
        try {
            $nombre = $request->nombre;
            $apellidos = $request->apellidos;
            $direccion = $request->direccion;
            $telefono = $request->telefono;
            $json = json_encode($request->json);

            $statement = "CALL sp_create_empleados_pagos(?,?,?,?,?)";
            $parameters = [$nombre, $apellidos, $direccion, $telefono, $json];
            $data = DB::select($statement, $parameters);
            return response()->json(['message' => "registro exitoso"], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function show($empleado)
    {
        try {
            $statement = "CALL sp_get_empleados_pago(?)";
            $parameters = [$empleado];
            $data = DB::select($statement, $parameters);
            return response()->json($data, 200);
        } catch (\throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function update(EmpleadosRequest $request, $empleado)
    {
        try {
            $nombre = $request->nombre;
            $apellidos = $request->apellidos;
            $direccion = $request->direccion;
            $telefono = $request->telefono;
            $json = json_encode($request->json);
            $jsonDelete = json_encode($request->jsonDelete);

            $statement = "CALL sp_update_empleados_pagos(?,?,?,?,?,?,?)";
            $parameters = [$empleado, $nombre, $apellidos, $direccion, $telefono, $json, $jsonDelete];
            $data = DB::select($statement, $parameters);

            return response()->json(['message' => "Actualizacion exitosa"], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function delete($empleado)
    {
        try {
            $statement = "CALL sp_eliminar_empleados_pagos(?)";
            $parameters = [$empleado];
            $data = DB::select($statement, $parameters);
            return response()->json(['message' => 'Registro eliminado'], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
