<?php

namespace App\Services\Fase3_RRHH;

use App\Models\Empleado;
use App\Services\AuditoriaService; // <-- IMPORTAMOS EL SERVICIO DE AUDITORÍA
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

        // <-- REGISTRAMOS LA CREACIÓN EN LA AUDITORÍA
        AuditoriaService::registrar("Registró al empleado: {$empleado->nombre} {$empleado->apellido} (Cédula: {$empleado->cedula})");

        return ['status' => 201, 'message' => 'Empleado registrado', 'data' => $empleado->load('cargo')];
    }

    public function listarEmpleados()
    {
        return ['status' => 200, 'data' => Empleado::with('cargo')->get()];
    }

    public function editarEmpleado(int $id, array $data)
    {
        $validator = Validator::make($data, [
            'nombre'   => 'required|string',
            'apellido' => 'required|string',
            'cedula'   => 'required|unique:empleados,cedula,' . $id,
            'cargo_id' => 'required|exists:cargos,id',
        ]);

        if ($validator->fails()) return ['status' => 400, 'errors' => $validator->errors()];

        $empleado = Empleado::findOrFail($id);
        $empleado->update($data);

        // <-- REGISTRAMOS LA MODIFICACIÓN EN LA AUDITORÍA
        AuditoriaService::registrar("Modificó los datos del empleado ID {$id}: {$empleado->nombre} {$empleado->apellido} (Cédula actual: {$empleado->cedula})");

        return [
            'status' => 200,
            'message' => 'Datos del empleado actualizados',
            'data' => $empleado->load('cargo')
        ];
    }
}