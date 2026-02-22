<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoCobrar extends Model
{
    protected $table = 'movimientos_cobrar';

 protected $fillable = [
    'id_cuentas_cobrar', // Verifica que en la DB sea plural
    'fecha',
    'monto_deuda',
    'monto_abono',
    'saldo',
    'estatus',
    'metodo_pago', // Agrégalo
    'observacion'  // Agrégalo
];

    // Relación: El abono pertenece a una cuenta por cobrar específica (Nivel 4)
    public function cuentaCobrar(): BelongsTo
    {
        return $this->belongsTo(CuentaCobrar::class, 'id_cuentas_cobrar');
    }
}