<?php

namespace App\Http\Controllers\Fase1;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\FormaPago;
use App\Models\MovimientoInventario;
use App\Models\CuentaCobrar;
use App\Models\MovimientoCobrar;
use App\Services\Fase1_InventarioYVentas\VentaService;
use App\Services\Fase1_InventarioYVentas\CreditoService;
use App\Services\Fase1_InventarioYVentas\CobranzaService;
use App\Services\Fase1_InventarioYVentas\ProductoService; // <-- IMPORTAMOS EL SERVICIO DE PRODUCTOS
use Illuminate\Http\Request;

class VentaController extends Controller
{
    protected $ventaService;
    protected $creditoService;
    protected $cobranzaService;
    protected $productoService; // <-- PROPIEDAD PARA EL SERVICIO DE PRODUCTOS

    /**
     * Inyección de dependencias de los microservicios.
     */
    public function __construct(
        VentaService $ventaService, 
        CreditoService $creditoService,
        CobranzaService $cobranzaService,
        ProductoService $productoService // <-- INYECTAMOS EL SERVICIO DE PRODUCTOS
    ) {
        $this->ventaService = $ventaService;
        $this->creditoService = $creditoService;
        $this->cobranzaService = $cobranzaService;
        $this->productoService = $productoService;
    }

    // --- GESTIÓN DE PRODUCTOS (ADMIN) ---

    public function index()
    {
        // Usamos el método del servicio para mantener consistencia
        $resultado = $this->productoService->obtenerTodos();
        return response()->json($resultado['data'], $resultado['status']);
    }

    public function storeProducto(Request $request)
    {
        // 🚀 LLAMAMOS AL SERVICIO: Aquí se valida, calcula el saldo y registra la AUDITORÍA
        $resultado = $this->productoService->crearProducto($request->all());
        return response()->json($resultado, $resultado['status']);
    }

    public function update(Request $request, $id)
    {
        // 🚀 LLAMAMOS AL SERVICIO: Actualiza los datos y registra el cambio en la AUDITORÍA
        $resultado = $this->productoService->actualizarProducto($id, $request->all());
        return response()->json($resultado, $resultado['status']);
    }

    // --- PROCESOS DE VENTA Y CRÉDITO ---

    /**
     * Venta Normal (Contado)
     */
    public function store(Request $request)
    {
        $resultado = $this->ventaService->procesarVenta($request->all());
        return response()->json($resultado, $resultado['status'] ?? 200);
    }

    /**
     * Venta a Crédito (Genera Deuda + Descuenta Stock)
     */
    public function crearVentaYEnviarACobrar(Request $request)
    {
        $resultado = $this->creditoService->registrarVentaACreditoCompleta($request->all());
        return response()->json($resultado, $resultado['status']);
    }

    /**
     * Registro de Cobros (Abonos a deudas existentes)
     */
    public function registrarPago(Request $request, $id)
    {
        $data = $request->all();
        $data['id_cuenta_cobrar'] = $id; 
    
        $resultado = $this->cobranzaService->registrarCobro($data);
        return response()->json($resultado, $resultado['status']);
    }

    // --- REPORTES Y AUDITORÍA ---

    public function indexVentas()
    {
        return response()->json(
            Venta::with(['cliente', 'formaPago', 'detalles.producto'])->latest()->get()
        );
    }

    public function indexCuentasPorCobrar()
    {
        return response()->json(
            CuentaCobrar::with(['venta.cliente', 'movimientos'])
                ->where('estatus', false)
                ->get()
        );
    }

    public function indexMovimientos()
    {
        return response()->json(
            MovimientoInventario::with(['producto', 'tipoMovimiento'])->latest()->get()
        );
    }

    public function indexHistorialCobros()
    {
        return response()->json(
            MovimientoCobrar::with(['cuentaCobrar.venta.cliente'])->latest()->get()
        );
    }

    public function indexClientes() { return response()->json(Cliente::all()); }
    public function indexFormasPago() { return response()->json(FormaPago::all()); }
}