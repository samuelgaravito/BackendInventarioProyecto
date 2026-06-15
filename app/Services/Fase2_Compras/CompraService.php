<?php

namespace App\Services\Fase2_Compras;

use App\Models\Compra;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use App\Models\DetalleCompra;
use App\Services\AuditoriaService; // <-- IMPORTAMOS EL SERVICIO DE AUDITORÍA
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CompraService
{
    public function procesarCompraContado(array $data)
    {
        $validator = Validator::make($data, [
            'id_acreedor'   => 'required|exists:acreedores,id',
            'id_forma_pago' => 'required|exists:formas_pago,id',
            'productos'     => 'required|array|min:1',
            'productos.*.id_producto'     => 'required|exists:productos,id',
            'productos.*.cantidad'        => 'required|numeric|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ['status' => 400, 'errors' => $validator->errors()];
        }

        return DB::transaction(function () use ($data) {
            try {
                $total = collect($data['productos'])->sum(fn($p) => $p['cantidad'] * $p['precio_unitario']);

                // 1. Cabecera
                $compra = Compra::create([
                    'id_acreedor'   => $data['id_acreedor'],
                    'id_forma_pago' => $data['id_forma_pago'],
                    'num_compra'    => 'COM-' . time(),
                    'monto_total'   => $total,
                    'fecha'         => now(),
                ]);

                // 2. Detalles e Inventario
                foreach ($data['productos'] as $item) {
                    DetalleCompra::create([
                        'id_compra'       => $compra->id,
                        'id_producto'     => $item['id_producto'],
                        'cantidad'        => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                        'importe'         => $item['cantidad'] * $item['precio_unitario']
                    ]);

                    // Actualizar existencia
                    Producto::find($item['id_producto'])->increment('existencia', $item['cantidad']);

                    // Kardex (Usando el nombre de columna costo_unitario que pide tu BD)
                    MovimientoInventario::create([
                        'id_producto'        => $item['id_producto'],
                        'id_tipo_movimiento' => 1, // Entrada
                        'cantidad'           => $item['cantidad'],
                        'costo_unitario'     => $item['precio_unitario'],
                        'fecha'              => now(),
                    ]);
                }

                // <-- REGISTRAMOS LA ACCIÓN EN LA AUDITORÍA
                AuditoriaService::registrar("Registró una compra de contado ({$compra->num_compra}) al proveedor/acreedor ID {$data['id_acreedor']} por un monto total de {$total}");

                return [
                    'status' => 201,
                    'data' => $compra->load('detalles')
                ];
            } catch (\Exception $e) {
                return ['status' => 500, 'message' => 'Error: ' . $e->getMessage()];
            }
        });
    }
}