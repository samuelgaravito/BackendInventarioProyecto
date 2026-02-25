<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Fase1\VentaController;
use App\Http\Controllers\Fase2\CompraController;
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
        Route::post('/ventas', [VentaController::class, 'store']); // Contado
        Route::post('/ventas/credito-directo', [VentaController::class, 'crearVentaYEnviarACobrar']); // Crédito

        // 3. Módulo de Cobranza (Gestión de Ingresos)
        // Cambiamos la definición para que acepte el parámetro ID
        Route::post('/cuentas-por-cobrar/pagar/{id}', [VentaController::class, 'registrarPago']);
        Route::get('/cuentas-por-cobrar/historial-pagos', [VentaController::class, 'indexHistorialCobros']); // Ver todos los abonos

        // 4. Auditoría y Consultas
        Route::get('/ventas-historial', [VentaController::class, 'indexVentas']);
        Route::get('/inventario/movimientos', [VentaController::class, 'indexMovimientos']);
        Route::get('/cuentas-por-cobrar', [VentaController::class, 'indexCuentasPorCobrar']); // Pendientes
    });

    /*
    |--------------------------------------------------------------------------
    | ACCESO EXCLUSIVO ADMIN (Mantenimiento de Catálogo)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::post('/productos', [VentaController::class, 'storeProducto']);
        Route::put('/productos/{id}', [VentaController::class, 'update']);
        // Rutas de Acreedores (Fase 2)
        // Acreedores
        Route::get('/acreedores', [CompraController::class, 'indexAcreedores']);
        Route::post('/acreedores', [CompraController::class, 'storeAcreedor']);

        // Compras
        Route::post('/compras', [CompraController::class, 'store']); // Contado
        Route::post('/compras-credito', [CompraController::class, 'crearCompraYEnviarAPagar']); // Crédito

        // Cuentas por Pagar
        Route::get('/cuentas-pagar', [CompraController::class, 'indexCuentasPagar']);
       
        // abono de pagos acreedores
       Route::post('/cuentas-pagar/{id}/abono', [CompraController::class, 'registrarAbonoProveedor']);
       
        });
});