<?php

namespace App\Services\Fase2_Compras;

use App\Models\Acreedor;
use Illuminate\Support\Facades\Validator;

class AcreedorService
{
    public function registrarAcreedor(array $data)
    {
        $validator = Validator::make($data, [
            'rif'       => 'required|unique:acreedores,rif',
            'nombre'    => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return ['status' => 400, 'errors' => $validator->errors()];
        }

        $acreedor = Acreedor::create($data);

        return [
            'status' => 201,
            'message' => 'Acreedor registrado con éxito',
            'data' => $acreedor
        ];
    }

    public function listarAcreedores()
{
    return [
        'status' => 200,
        'data' => Acreedor::orderBy('nombre', 'asc')->get()
    ];
}
}