<?php

namespace App\Services\Fase1_InventarioYVentas;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\CuentaCobrar;
use App\Models\Cliente;
use App\Services\AuditoriaService; // <-- IMPORTAMOS EL SERVICIO DE AUDITORÍA
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreditoService
{
    protected $productoService;

    // Inyectamos ProductoService para manejar el stock igual que en ventas normales
    public function __construct(ProductoService $productoService)
    {
        $this->productoService = $productoService;
    }

    public function registrarVentaACreditoCompleta(array $data)
    {
        // 1. Validaciones (Estructura idéntica a venta normal + campos de crédito)
        $validator = Validator::make($data, [
            'cliente_cedula'    => 'required|string',
            'cliente_nombre'    => 'required|string',
            'id_forma_pago'     => 'required|exists:formas_pago,id',
            'plazo_pago'        => 'required|string',
            'dias_vencimiento'  => 'required|integer',
            'productos'         => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:productos,id',
            'productos.*.cantidad'    => 'required|numeric|min:1',
            'productos.*.precio_unitario' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return ['status' => 400, 'errors' => $validator->errors()];
        }

        return DB::transaction(function () use ($data) {
            try {
                // 2. Gestión de Cliente
                $cliente = Cliente::firstOrCreate(
                    ['cedula' => $data['cliente_cedula']],
                    ['nombre' => $data['cliente_nombre'], 'direccion' => $data['cliente_direccion'] ?? 'Sin dirección']
                );

                $totalVenta = 0;
                $detallesParaGuardar = [];

                // 3. Validación de Stock y Cálculo (Igual que venta normal)
                foreach ($data['productos'] as $item) {
                    $resStock = $this->productoService->validarYDescontarStock($item['id_producto'], $item['cantidad']);
                    if ($resStock['status'] !== 200) return $resStock;

                    $importe = $item['cantidad'] * $item['precio_unitario'];
                    $totalVenta += $importe;

                    $detallesParaGuardar[] = [
                        'id_producto' => $item['id_producto'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                        'importe' => $importe
                    ];
                }

                // 4. Crear la Venta
                $venta = Venta::create([
                    'id_cliente' => $cliente->id,
                    'num_venta' => 'VNT-CRED-' . time(),
                    'id_forma_pago' => $data['id_forma_pago'],
                    'monto_total' => $totalVenta,
                    'fecha' => now(),
                ]);

                // 5. Guardar Detalles y Kardex
                foreach ($detallesParaGuardar as $det) {
                    DetalleVenta::create(array_merge($det, ['id_venta' => $venta->id]));
                    MovimientoInventario::create([
                        'id_producto' => $det['id_producto'],
                        'id_tipo_movimiento' => 2, // Salida
                        'cantidad' => $det['cantidad'],
                        'costo_unitario' => $det['precio_unitario'],
                        'fecha' => now(),
                    ]);
                }

                // 6. EL PLUS: Crear la Cuenta por Cobrar
                $cuenta = CuentaCobrar::create([
                    'id_cliente'        => $cliente->id,
                    'id_venta'          => $venta->id,
                    'fecha_emision'     => now(),
                    'fecha_vencimiento' => now()->addDays($data['dias_vencimiento']),
                    'plazo_pago'        => $data['plazo_pago'],
                    'monto'             => $totalVenta,
                    'estatus'           => false, // pendiente
                ]);

                // <-- REGISTRAMOS LA ACCIÓN EN LA AUDITORÍA
                AuditoriaService::registrar("Registró una venta a crédito ({$venta->num_venta}) para el cliente {$cliente->nombre} (Cédula: {$cliente->cedula}) por un monto total de {$totalVenta}");

                return [
                    'status' => 201,
                    'message' => 'Venta a crédito y stock procesados correctamente',
                    'data' => $venta->load('cliente', 'detalles')
                ];

            } catch (\Exception $e) {
                return ['status' => 500, 'message' => 'Error: ' . $e->getMessage()];
            }
        });
    }
}