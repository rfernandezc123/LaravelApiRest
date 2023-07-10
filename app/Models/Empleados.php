<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleados extends Model
{
    protected $table = "empleados";
    protected $id = "id";
    protected $fillable = ["nombre", "apellidos", "direccion", "telefono"];

    use HasFactory;
}
