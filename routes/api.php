<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Fase1\VentaController;
use App\Http\Controllers\Fase2\CompraController;
use App\Http\Controllers\Fase3\NominaController;
use App\Models\Auditoria;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- RUTAS PÚBLICAS ---
Route::middleware('guest')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// --- RUTAS PROTEGIDAS (AUTH:SANCTUM) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Perfil del usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles');
    });
    
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | ACCESO PARA VENDEDORES Y ADMINS (Gestión Operativa)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:vendedor|admin')->group(function () {
        
        // 1. Listados Base (Selectores y Catálogos)
        Route::get('/productos', [VentaController::class, 'index']);
        Route::get('/productos/{id}', [VentaController::class, 'show']);
        Route::get('/clientes', [VentaController::class, 'indexClientes']);
        Route::get('/formas-pago', [VentaController::class, 'indexFormasPago']);

        // 2. Procesos de Venta (Flujos de Salida)
        Route::post('/ventas', [VentaController::class, 'store']); 
        Route::post('/ventas/credito-directo', [VentaController::class, 'crearVentaYEnviarACobrar']); 

        // 3. Módulo de Cobranza (Gestión de Ingresos)
        Route::post('/cuentas-por-cobrar/pagar/{id}', [VentaController::class, 'registrarPago']);
        Route::get('/cuentas-por-cobrar/historial-pagos', [VentaController::class, 'indexHistorialCobros']); 

        // 4. Auditoría y Consultas
        Route::get('/ventas-historial', [VentaController::class, 'indexVentas']);
        Route::get('/inventario/movimientos', [VentaController::class, 'indexMovimientos']);
        Route::get('/cuentas-por-cobrar', [VentaController::class, 'indexCuentasPorCobrar']); 
    });

    /*
    |--------------------------------------------------------------------------
    | ACCESO EXCLUSIVO ADMIN (Fase 2 y Fase 3)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        // --- AUDITORIA ---
        Route::get('/auditoria', [NominaController::class, 'indexAuditoria']);
        // --- MANTENIMIENTO PRODUCTOS ---
        Route::post('/productos', [VentaController::class, 'storeProducto']);
        Route::put('/productos/{id}', [VentaController::class, 'update']);

        // --- FASE 2: COMPRAS Y ACREEDORES ---
        Route::get('/acreedores', [CompraController::class, 'indexAcreedores']);
        Route::post('/acreedores', [CompraController::class, 'storeAcreedor']);
        Route::post('/compras', [CompraController::class, 'store']); 
        Route::post('/compras-credito', [CompraController::class, 'crearCompraYEnviarAPagar']); 
        Route::get('/cuentas-pagar', [CompraController::class, 'indexCuentasPagar']);
        Route::post('/cuentas-pagar/{id}/abono', [CompraController::class, 'registrarAbonoProveedor']);

        // --- FASE 3: RRHH Y NÓMINA (NUEVAS RUTAS) ---
        // Gestión de Cargos
        Route::get('/cargos', [NominaController::class, 'indexCargos']);
        Route::post('/cargos', [NominaController::class, 'storeCargo']);
        Route::put('/cargos/{id}', [NominaController::class, 'updateCargo']);

        // Gestión de Empleados
        Route::get('/empleados', [NominaController::class, 'indexEmpleados']);
        Route::post('/empleados', [NominaController::class, 'storeEmpleado']);
        Route::put('/empleados/{id}', [NominaController::class, 'updateEmpleado']);

        // Gestión de Nómina 
        Route::get('/nominas', [NominaController::class, 'indexNominas']);
        Route::post('/nominas', [NominaController::class, 'storeNomina']);
        Route::put('/nominas/{id}', [NominaController::class, 'updateNomina']);
    });
});