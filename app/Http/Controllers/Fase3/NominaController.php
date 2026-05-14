<?php

namespace App\Http\Controllers\Fase3;

use App\Http\Controllers\Controller;
use App\Services\Fase3_RRHH\CargoService;
use App\Services\Fase3_RRHH\EmpleadoService;
use Illuminate\Http\Request;

class NominaController extends Controller
{
    protected $cargoService;
    protected $empleadoService;

    public function __construct(CargoService $cargoService, EmpleadoService $empleadoService)
    {
        $this->cargoService = $cargoService;
        $this->empleadoService = $empleadoService;
    }

    // CARGOS
    public function storeCargo(Request $request) {
        $res = $this->cargoService->registrarCargo($request->all());
        return response()->json($res, $res['status']);
    }

    public function indexCargos() {
        $res = $this->cargoService->listarCargos();
        return response()->json($res['data'], $res['status']);
    }

    // EMPLEADOS
    public function storeEmpleado(Request $request) {
        $res = $this->empleadoService->registrarEmpleado($request->all());
        return response()->json($res, $res['status']);
    }

    public function indexEmpleados() {
        $res = $this->empleadoService->listarEmpleados();
        return response()->json($res['data'], $res['status']);
    }
}