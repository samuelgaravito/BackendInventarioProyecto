<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nomina extends Model
{
    // AGREGA ESTA LÍNEA PARA CORREGIR EL ERROR
    protected $table = 'nominas'; 

    protected $fillable = [
        'id_empleado',
        'dias_trabajados',
        'fecha',
        'ivss',
        'faov',
        'paro_forzoso',
        'caja_ahorro',
        'cesta_ticket_dia',
        'cesta_ticket_recibir',
        'salario_quincenal',
        'salario_mensual'
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }
}