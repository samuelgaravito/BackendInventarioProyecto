<?php

namespace App\Services\Fase2_Compras;

use App\Models\CuentaPagar;
use App\Services\AuditoriaService; // <-- IMPORTAMOS EL SERVICIO PARA MANTENER LA ARQUITECTURA
use Exception;

class CuentaPagarService
{
    /**
     * Listar todas las cuentas por pagar pendientes.
     */
    public function listarPendientes()
    {
        try {
            // Buscamos las deudas que tengan estatus false (no pagadas)
            $cuentas = CuentaPagar::with(['acreedor', 'compra'])
                ->where('estatus', false)
                ->orderBy('created_at', 'desc')
                ->get();

            return [
                'status' => 200,
                'data' => $cuentas
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => 'Error al obtener deudas: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Ver el detalle de una cuenta específica por su ID.
     */
    public function verDetalle($id)
    {
        $cuenta = CuentaPagar::with(['acreedor', 'compra.detalles.producto'])->find($id);

        if (!$cuenta) {
            return ['status' => 404, 'message' => 'Cuenta no encontrada'];
        }

        return ['status' => 200, 'data' => $cuenta];
    }
}