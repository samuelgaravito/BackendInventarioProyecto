<?php

namespace App\Services\Fase2_Compras;

use App\Models\CuentaPagar;
use App\Services\AuditoriaService; // <-- IMPORTAMOS EL SERVICIO DE AUDITORÍA
use Illuminate\Support\Facades\DB;

class CreditoCompraService
{
    protected $compraService;

    public function __construct(CompraService $compraService)
    {
        $this->compraService = $compraService;
    }

    public function registrarCompraACreditoCompleta(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Reutilizamos el servicio de contado para stock y cabecera
            $resultado = $this->compraService->procesarCompraContado($data);

            if ($resultado['status'] !== 201) {
                return $resultado;
            }

            $compra = $resultado['data'];

            // Creamos el registro de la deuda
            CuentaPagar::create([
                'id_acreedor'  => $compra->id_acreedor,
                'id_compra'    => $compra->id,
                'deuda_total'  => $compra->monto_total,
                'deuda_actual' => $compra->monto_total,
                'estatus'      => false, // Pendiente
                'fecha'        => now(),
            ]);

            // <-- REGISTRAMOS LA ACCIÓN EN LA AUDITORÍA
            AuditoriaService::registrar("Registró una compra a crédito ({$compra->num_compra}) al proveedor ID {$compra->id_acreedor} generando una cuenta por pagar de {$compra->monto_total}");

            return [
                'status' => 201,
                'message' => 'Compra a crédito y cuenta por pagar registradas',
                'data' => $compra
            ];
        });
    }
}