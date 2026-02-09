<?php

namespace App\Services\Fase1_InventarioYVentas;

use App\Models\Producto;
use Illuminate\Support\Facades\Validator;

class ProductoService
{
    public function obtenerTodos()
    {
        return [
            'status' => 200,
            'data' => Producto::all()
        ];
    }

    public function obtenerPorId($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return [
                'status' => 404,
                'message' => "Producto no encontrado."
            ];
        }

        return [
            'status' => 200,
            'data' => $producto
        ];
    }

    public function crearProducto(array $data)
    {
        $validator = Validator::make($data, [
            'referencia' => 'required|unique:productos,referencia',
            'descripcion' => 'required|string|max:255',
            'costo_unitario' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'existencia' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 400,
                'errors' => $validator->errors()
            ];
        }

        // Cálculo de saldo inicial automático antes de guardar
        $data['saldo'] = $data['existencia'] * $data['costo_unitario'];
        
        $producto = Producto::create($data);

        return [
            'status' => 201,
            'message' => 'Producto creado exitosamente',
            'data' => $producto
        ];
    }

    public function actualizarProducto($id, array $data)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return ['status' => 404, 'message' => "Producto no encontrado."];
        }

        $validator = Validator::make($data, [
            'referencia' => 'sometimes|unique:productos,referencia,' . $id,
            'descripcion' => 'sometimes|string',
            'costo_unitario' => 'sometimes|numeric',
            'precio_venta' => 'sometimes|numeric',
            'existencia' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return ['status' => 400, 'errors' => $validator->errors()];
        }

        $producto->update($data);

        return [
            'status' => 200,
            'message' => 'Producto actualizado correctamente',
            'data' => $producto
        ];
    }

    // Esta es la función que usa el VentaService
    public function validarYDescontarStock($productoId, $cantidad)
    {
        $producto = Producto::find($productoId);

        if (!$producto) {
            return ['status' => 404, 'message' => "Producto ID {$productoId} no encontrado."];
        }

        if ($producto->existencia < $cantidad) {
            return [
                'status' => 422, 
                'message' => "Stock insuficiente para '{$producto->descripcion}'. Disponible: {$producto->existencia}."
            ];
        }

        $producto->existencia -= $cantidad;
        $producto->save();

        return ['status' => 200, 'producto' => $producto];
    }
}