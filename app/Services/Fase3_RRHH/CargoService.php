<?php

namespace App\Services\Fase3_RRHH;

use App\Models\Cargo;
use Illuminate\Support\Facades\Validator;

class CargoService
{
    public function registrarCargo(array $data)
    {
        $validator = Validator::make($data, [
            'descripcion' => 'required|string|unique:cargos,descripcion',
            'sueldo_base' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) return ['status' => 400, 'errors' => $validator->errors()];

        $cargo = Cargo::create($data);
        return ['status' => 201, 'message' => 'Cargo creado con éxito', 'data' => $cargo];
    }

    public function listarCargos()
    {
        return ['status' => 200, 'data' => Cargo::all()];
    }
}