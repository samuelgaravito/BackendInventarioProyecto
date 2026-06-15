<?php

namespace App\Services;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditoriaService
{
    /**
     * Registra una acción en la tabla de auditoría de forma automática.
     *
     * @param string $descripcion Texto descriptivo de la acción (Ej: "Creó el empleado V-123456")
     */
    public static function registrar(string $descripcion): void
    {
        Auditoria::create([
            'user_id'     => Auth::id(), // Captura automáticamente el ID del usuario autenticado
            'descripcion' => $descripcion,
            'ip'          => Request::ip(), // Captura automáticamente la IP del cliente
        ]);
    }
}