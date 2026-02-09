<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoPagar extends Model
{
    protected $table = 'movimientos_pagar';

    protected $fillable = [
        'id_cuentas_pagar',
        'fecha_movimiento',
        'monto_abono',
        'estatus'
    ];

    // Relación: El movimiento pertenece a una cuenta por pagar específica (Nivel 4)
    public function cuentaPagar(): BelongsTo
    {
        return $this->belongsTo(CuentaPagar::class, 'id_cuentas_pagar');
    }
}