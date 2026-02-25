<?php

namespace App\Http\Controllers\Fase2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Servicios de la Fase 2
use App\Services\Fase2_Compras\AcreedorService;
use App\Services\Fase2_Compras\CompraService;
use App\Services\Fase2_Compras\CreditoCompraService;
use App\Services\Fase2_Compras\CuentaPagarService;
use App\Services\Fase2_Compras\PagosService;

// Modelos para reportes rápidos
use App\Models\Compra;
use App\Models\MovimientoPagar;

class CompraController extends Controller
{
    protected $acreedorService;
    protected $compraService;
    protected $creditoService;
    protected $cuentaPagarService;
    protected $pagosService;

    /**
     * Inyección de los 5 servicios necesarios para la Fase 2.
     */
    public function __construct(
        AcreedorService $acreedorService,
        CompraService $compraService, 
        CreditoCompraService $creditoService,
        CuentaPagarService $cuentaPagarService,
        PagosService $pagosService
    ) {
        $this->acreedorService = $acreedorService;
        $this->compraService = $compraService;
        $this->creditoService = $creditoService;
        $this->cuentaPagarService = $cuentaPagarService;
        $this->pagosService = $pagosService;
    }

    // --- GESTIÓN DE ACREEDORES (PROVEEDORES) ---

    public function indexAcreedores()
    {
        $res = $this->acreedorService->listarAcreedores();
        return response()->json($res['data'], $res['status']);
    }

    public function storeAcreedor(Request $request)
    {
        $res = $this->acreedorService->registrarAcreedor($request->all());
        return response()->json($res, $res['status']);
    }

    // --- PROCESOS DE COMPRA ---

    /**
     * Venta Normal (Contado)
     */
    public function store(Request $request)
    {
        $res = $this->compraService->procesarCompraContado($request->all());
        return response()->json($res, $res['status']);
    }

    /**
     * Venta a Crédito (Genera Deuda + Aumenta Stock)
     */
    public function crearCompraYEnviarAPagar(Request $request)
    {
        $res = $this->creditoService->registrarCompraACreditoCompleta($request->all());
        return response()->json($res, $res['status']);
    }

    // --- GESTIÓN DE CUENTAS POR PAGAR Y ABONOS ---

    /**
     * Registro de Pagos (Abonos a deudas con proveedores)
     */
public function registrarAbonoProveedor(Request $request, $id)
    {
        $data = $request->all();
        $data['id_cuenta_pagar'] = $id; // Inyectamos el ID de la URL
    
        $res = $this->pagosService->registrarAbono($data);
        return response()->json($res, $res['status']);
    }

    public function indexCuentasPagar()
    {
        // Solo enviamos las deudas pendientes (estatus false)
        $res = $this->cuentaPagarService->listarPendientes();
        return response()->json($res['data'], $res['status']);
    }

    // --- REPORTES Y AUDITORÍA ---

    public function indexCompras()
    {
        return response()->json(
            Compra::with(['acreedor', 'detalles.producto'])->latest()->get()
        );
    }

    public function indexHistorialPagos()
    {
        // Historial de la tabla movimientos_pagar
        return response()->json(
            MovimientoPagar::with(['cuentaPagar.compra', 'cuentaPagar.acreedor'])->latest()->get()
        );
    }
}