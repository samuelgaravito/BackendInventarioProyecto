<?php

namespace App\Services\Fase1_InventarioYVentas;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Cliente; // Importamos el modelo Cliente
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VentaService
{
    protected $productoService;

    public function __construct(ProductoService $productoService)
    {
        $this->productoService = $productoService;
    }

    public function crearVentaCompleta(array $requestData)
    {
        // 1. Validaciones de estructura (Cambiamos id_cliente por datos del cliente)
        $validator = Validator::make($requestData, [
            'cliente_cedula' => 'required|string', 
            'cliente_nombre' => 'required_if:cliente_nuevo,true|string',
            'id_forma_pago' => 'required|exists:formas_pago,id',
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|numeric|min:1',
            'productos.*.precio_unitario' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return ['status' => 400, 'errors' => $validator->errors()];
        }

        return DB::transaction(function () use ($requestData) {
            try {
                // 2. Lógica de Cliente: Buscar o Crear
                $cliente = Cliente::firstOrCreate(
                    ['cedula' => $requestData['cliente_cedula']],
                    [
                        'nombre' => $requestData['cliente_nombre'] ?? 'Consumidor Final',
                        'telefono' => $requestData['cliente_telefono'] ?? null,
                        'direccion' => $requestData['cliente_direccion'] ?? 'Sin dirección',
                    ]
                );

                $totalVenta = 0;
                $detallesParaGuardar = [];

                // 3. Validar stock de todos los productos
                foreach ($requestData['productos'] as $item) {
                    $resultadoStock = $this->productoService->validarYDescontarStock(
                        $item['id_producto'], 
                        $item['cantidad']
                    );

                    if ($resultadoStock['status'] !== 200) {
                        return $resultadoStock; 
                    }

                    $importe = $item['cantidad'] * $item['precio_unitario'];
                    $totalVenta += $importe;

                    $detallesParaGuardar[] = [
                        'id_producto' => $item['id_producto'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                        'importe' => $importe
                    ];
                }

                // 4. Crear Cabecera de Venta vinculada al cliente (encontrado o creado)
                $venta = Venta::create([
                    'id_cliente' => $cliente->id, // ID obtenido automáticamente
                    'num_venta' => 'VENT-' . time(),
                    'id_forma_pago' => $requestData['id_forma_pago'],
                    'monto_total' => $totalVenta,
                    'fecha' => now(),
                ]);

                // 5. Guardar Detalles y Movimientos
                foreach ($detallesParaGuardar as $det) {
                    DetalleVenta::create(array_merge($det, ['id_venta' => $venta->id]));
                    
                    MovimientoInventario::create([
                        'id_producto' => $det['id_producto'],
                        'id_tipo_movimiento' => 2, 
                        'cantidad' => $det['cantidad'],
                        'costo_unitario' => $det['precio_unitario'],
                        'fecha' => now(),
                    ]);
                }

                return [
                    'status' => 201,
                    'message' => 'Venta y Cliente procesados correctamente',
                    'data' => $venta->load('cliente', 'detalles')
                ];

            } catch (\Exception $e) {
                return [
                    'status' => 500,
                    'message' => 'Error crítico',
                    'error' => $e->getMessage()
                ];
            }
        });
    }
}