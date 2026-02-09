<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nomina extends Model
{
    protected $table = 'nomina'; // [cite: 35]

    protected $fillable = [
        'id_empleado',
        'dias_trabajados',
        'ivss',
        'faov',
        'paro_forzoso',
        'caja_ahorro',
        'cesta_ticket_dia',
        'cesta_ticket_recibir',
        'salario_quincenal',
        'salario_mensual',
        'fecha'
    ]; // [cite: 69, 79, 144, 145]

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'id_empleado'); // [cite: 69]
    }
}