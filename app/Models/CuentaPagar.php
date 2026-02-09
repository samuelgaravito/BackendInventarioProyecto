<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuentaPagar extends Model
{
    protected $table = 'cuentas_pagar'; 

    protected $fillable = [
        'id_acreedor',
        'id_compra',
        'fecha',
        'descripcion',
        'deuda_total',
        'deuda_actual',
        'estatus'
    ]; 

    public function acreedor(): BelongsTo
    {
        return $this->belongsTo(Acreedor::class, 'id_acreedor'); 
}
    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'id_compra'); 
    }

    // Relación: Una cuenta por pagar tiene muchos abonos (Nivel 5)
    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoPagar::class, 'id_cuentas_pagar');
    }
}