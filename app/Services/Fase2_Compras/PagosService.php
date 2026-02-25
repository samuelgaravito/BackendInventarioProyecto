<?php

namespace App\Services\Fase2_Compras;

use App\Models\CuentaPagar;
use App\Models\MovimientoPagar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PagosService
{
    public function registrarAbono(array $data)
    {
        // Validamos la entrada desde el controlador
        $validator = Validator::make($data, [
            'id_cuenta_pagar' => 'required|exists:cuentas_pagar,id',
            'monto_abono'     => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return ['status' => 400, 'errors' => $validator->errors()];
        }

        return DB::transaction(function () use ($data) {
            // Buscamos la deuda
            $cuenta = CuentaPagar::findOrFail($data['id_cuenta_pagar']);

            // 1. Regla de negocio: No se puede abonar más de lo que se debe
            if ($data['monto_abono'] > $cuenta->deuda_actual) {
                return [
                    'status' => 400, 
                    'message' => "El abono ({$data['monto_abono']}) supera la deuda actual ({$cuenta->deuda_actual})"
                ];
            }

            // 2. Crear el registro en movimientos_pagar
            // Usamos los nombres exactos de tu modelo MovimientoPagar
            MovimientoPagar::create([
                'id_cuentas_pagar' => $cuenta->id,    // Plural según tu modelo
                'fecha_movimiento'   => now(),           // Obligatorio en BD
                'monto_abono'      => $data['monto_abono'],
                'estatus'          => true
            ]);

            // 3. Actualizar la cabecera de la deuda
            $cuenta->deuda_actual -= $data['monto_abono'];
            
            // Si la deuda llega a cero, marcamos como pagado
            if ($cuenta->deuda_actual <= 0) {
                $cuenta->deuda_actual = 0;
                $cuenta->estatus = true; 
            }
            
            $cuenta->save();

            return [
                'status' => 201,
                'message' => 'Abono registrado correctamente',
                'data' => [
                    'saldo_restante' => $cuenta->deuda_actual,
                    'completado' => $cuenta->estatus
                ]
            ];
        });
    }
}