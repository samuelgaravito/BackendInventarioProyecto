<?php

namespace App\Services\Fase3_RRHH;

use App\Models\Empleado;
use Illuminate\Support\Facades\Validator;

class EmpleadoService
{
    public function registrarEmpleado(array $data)
    {
        $validator = Validator::make($data, [
            'nombre'   => 'required|string',
            'apellido' => 'required|string',
            'cedula'   => 'required|unique:empleados,cedula',
            'cargo_id' => 'required|exists:cargos,id',
        ]);

        if ($validator->fails()) return ['status' => 400, 'errors' => $validator->errors()];

        $empleado = Empleado::create($data);
        return ['status' => 201, 'message' => 'Empleado registrado', 'data' => $empleado->load('cargo')];
    }

    public function listarEmpleados()
    {
        return ['status' => 200, 'data' => Empleado::with('cargo')->get()];
    }
}