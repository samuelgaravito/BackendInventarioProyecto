<?php

namespace App\Http\Controllers\Fase1;

use App\Http\Controllers\Controller;
use App\Services\Fase1_InventarioYVentas\VentaService;
use App\Services\Fase1_InventarioYVentas\ProductoService;
use Illuminate\Http\Request;
use Exception;

class VentaController extends Controller
{
    protected $ventaService;
    protected $productoService;

    public function __construct(VentaService $ventaService, ProductoService $productoService)
    {
        $this->ventaService = $ventaService;
        $this->productoService = $productoService;
    }

    // --- AGREGA ESTE MÉTODO PARA REPARAR EL ERROR DE POSTMAN ---
    public function index()
    {
        $res = $this->productoService->obtenerTodos();
        return response()->json($res, $res['status']);
    }

    // --- AGREGA ESTE MÉTODO PARA PODER CREAR PRODUCTOS ---
    public function storeProducto(Request $request)
    {
        $res = $this->productoService->crearProducto($request->all());
        return response()->json($res, $res['status']);
    }

    // Tu método de ventas que ya tenías
    public function store(Request $request)
    {
        $resultado = $this->ventaService->crearVentaCompleta($request->all());
        return response()->json($resultado, $resultado['status']);
    }
}