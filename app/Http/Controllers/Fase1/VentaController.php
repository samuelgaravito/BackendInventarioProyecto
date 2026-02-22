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
use App\Services\Fase1_InventarioYVentas\CobranzaService; // Nuevo Servicio
use Illuminate\Http\Request;

class VentaController extends Controller
{
    protected $ventaService;
    protected $creditoService;
    protected $cobranzaService;

    /**
     * Inyección de dependencias de los 3 microservicios.
     */
    public function __construct(
        VentaService $ventaService, 
        CreditoService $creditoService,
        CobranzaService $cobranzaService
    ) {
        $this->ventaService = $ventaService;
        $this->creditoService = $creditoService;
        $this->cobranzaService = $cobranzaService;
    }

    // --- GESTIÓN DE PRODUCTOS (ADMIN) ---

    public function index()
    {
        return response()->json(Producto::all());
    }

    public function storeProducto(Request $request)
    {
        $validated = $request->validate([
            'referencia' => 'required|unique:productos',
            'descripcion' => 'required',
            'costo_unitario' => 'required|numeric',
            'precio_venta' => 'required|numeric',
            'existencia' => 'required|integer',
        ]);

        $producto = Producto::create($validated);
        return response()->json($producto, 201);
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        $producto->update($request->all());
        return response()->json(['message' => 'Producto actualizado', 'producto' => $producto]);
    }

    // --- PROCESOS DE VENTA Y CRÉDITO ---

    /**
     * Venta Normal (Contado)
     */
    public function store(Request $request)
    {
        // Recuerda que en el Service renombramos a procesarVenta
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
        // Agregamos el ID de la URL al array de datos para que el Service lo valide
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
        // Solo enviamos las que aún no están pagadas (estatus false)
        return response()->json(
            CuentaCobrar::with(['venta.cliente', 'movimientos'])
                ->where('estatus', false)
                ->get()
        );
    }

    public function indexMovimientos()
    {
        // Kardex de inventario
        return response()->json(
            MovimientoInventario::with(['producto', 'tipoMovimiento'])->latest()->get()
        );
    }

    public function indexHistorialCobros()
    {
        // Historial de la tabla movimientos_cobrar
        return response()->json(
            MovimientoCobrar::with(['cuentaCobrar.venta.cliente'])->latest()->get()
        );
    }

    // Listados simples para selectores en el Front
    public function indexClientes() { return response()->json(Cliente::all()); }
    public function indexFormasPago() { return response()->json(FormaPago::all()); }
}