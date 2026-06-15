<?php

namespace App\Services\Fase1_InventarioYVentas;

use App\Models\CuentaCobrar;
use App\Models\MovimientoCobrar; 
use App\Services\AuditoriaService; // <-- IMPORTAMOS EL SERVICIO DE AUDITORÍA
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CobranzaService
{
    public function registrarCobro(array $data)
    {
        $validator = Validator::make($data, [
            'id_cuenta_cobrar' => 'required|exists:cuentas_cobrar,id',
            'monto_pagado'     => 'required|numeric|min:0.01',
            'fecha_pago'       => 'required|date',
        ]);

        if ($validator->fails()) {
            return ['status' => 400, 'errors' => $validator->errors()];
        }

        return DB::transaction(function () use ($data) {
            $cuenta = CuentaCobrar::findOrFail($data['id_cuenta_cobrar']);

            // 1. Calcular totales
            $totalAbonadoAntes = MovimientoCobrar::where('id_cuentas_cobrar', $cuenta->id)->sum('monto_abono');
            $nuevoSaldo = $cuenta->monto - ($totalAbonadoAntes + $data['monto_pagado']);

            // 2. Crear movimiento SOLO con las columnas que existen en tu DB
            MovimientoCobrar::create([
                'id_cuentas_cobrar' => $cuenta->id,
                'fecha'             => $data['fecha_pago'],
                'monto_deuda'       => $cuenta->monto,
                'monto_abono'       => $data['monto_pagado'],
                'saldo'             => max(0, $nuevoSaldo),
                'estatus'           => ($nuevoSaldo <= 0)
            ]);

            // 3. Actualizar cuenta principal
            if ($nuevoSaldo <= 0) {
                $cuenta->update(['estatus' => true]); 
            }

            // <-- REGISTRAMOS LA ACCIÓN EN LA AUDITORÍA
            $saldoFormateado = max(0, $nuevoSaldo);
            AuditoriaService::registrar("Registró un cobro/abono de {$data['monto_pagado']} a la cuenta por cobrar ID: {$cuenta->id}. Saldo restante: {$saldoFormateado}");

            return [
                'status' => 201,
                'message' => '¡Cobro registrado con éxito!',
                'detalle' => [
                    'saldo_pendiente' => max(0, $nuevoSaldo),
                    'total_pagado'    => $totalAbonadoAntes + $data['monto_pagado']
                ]
            ];
        });
    }
}