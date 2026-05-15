<?php

namespace App\Http\Controllers\Fase3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Inyección de los 3 servicios de la Fase 3
use App\Services\Fase3_RRHH\CargoService;
use App\Services\Fase3_RRHH\EmpleadoService;
use App\Services\Fase3_RRHH\NominaService;

class NominaController extends Controller
{
    protected $cargoService;
    protected $empleadoService;
    protected $nominaService;

    /**
     * Constructor con Inyección de Dependencias.
     * Mantenemos la arquitectura de servicios para limpiar la lógica del controlador.
     */
    public function __construct(
        CargoService $cargoService, 
        EmpleadoService $empleadoService,
        NominaService $nominaService
    ) {
        $this->cargoService = $cargoService;
        $this->empleadoService = $empleadoService;
        $this->nominaService = $nominaService;
    }

    // --- 1. GESTIÓN DE CARGOS ---

    public function indexCargos()
    {
        $res = $this->cargoService->listarCargos();
        return response()->json($res['data'], $res['status']);
    }

    public function storeCargo(Request $request)
    {
        $res = $this->cargoService->registrarCargo($request->all());
        return response()->json($res, $res['status']);
    }

    public function updateCargo(Request $request, $id)
    {
        $res = $this->cargoService->editarCargo($id, $request->all());
        return response()->json($res, $res['status']);
    }

    // --- 2. GESTIÓN DE EMPLEADOS ---

    public function indexEmpleados()
    {
        $res = $this->empleadoService->listarEmpleados();
        return response()->json($res['data'], $res['status']);
    }

    public function storeEmpleado(Request $request)
    {
        $res = $this->empleadoService->registrarEmpleado($request->all());
        return response()->json($res, $res['status']);
    }

    public function updateEmpleado(Request $request, $id)
    {
        $res = $this->empleadoService->editarEmpleado($id, $request->all());
        return response()->json($res, $res['status']);
    }
    // --- 3. CÁLCULO Y GESTIÓN DE NÓMINA ---

    /**
     * Generar un nuevo registro de pago para un empleado.
     * Calcula deducciones (IVSS, FAOV) y beneficios (Cesta Ticket).
     */
    public function storeNomina(Request $request)
    {
        $res = $this->nominaService->generarNomina($request->all());
        return response()->json($res, $res['status']);
    }

    /**
     * Historial general de pagos realizados.
     */
    public function indexNominas()
    {
        $res = $this->nominaService->historial();
        return response()->json($res['data'], $res['status']);
    }

    public function updateNomina(Request $request, $id)
    {
        $res = $this->nominaService->editarNomina($id, $request->all());
        return response()->json($res, $res['status']);
    }
}