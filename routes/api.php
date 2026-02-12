<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Fase1\VentaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- RUTAS PÚBLICAS (GUEST) ---
Route::middleware('guest')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// --- RUTAS PROTEGIDAS (AUTH:SANCTUM) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Perfil y Logout
    Route::get('/user', function (Request $request) {
        // En Spatie, la relación se suele llamar 'roles'
        return $request->user()->load('roles');
    });
    
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | ACCESO PARA VENDEDORES Y ADMINS
    |--------------------------------------------------------------------------
    | Se utiliza el middleware 'role' de Spatie. 
    | Nota: Si usas Laravel 11, asegúrate de haber registrado el alias en bootstrap/app.php
    */
    Route::middleware('role:vendedor|admin')->group(function () {
        // Pueden ver productos y procesar ventas
        Route::get('/productos', [VentaController::class, 'index']);
        Route::get('/productos/{id}', [VentaController::class, 'show']);
        Route::post('/ventas', [VentaController::class, 'store']);
        Route::get('/clientes', [VentaController::class, 'indexClientes']);
        Route::get('/formas-pago', [VentaController::class, 'indexFormasPago']);
    });

    /*
    |--------------------------------------------------------------------------
    | ACCESO EXCLUSIVO ADMIN (Gestión de Stock y Configuración)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        // Solo el admin crea o edita la mercancía
        Route::post('/productos', [VentaController::class, 'storeProducto']);
        Route::put('/productos/{id}', [VentaController::class, 'update']);
        
        Route::get('/admin/stats', function () {
            return response()->json(['message' => 'Panel de control administrativo']);
        });
    });

    // Verificación de Email
    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
});