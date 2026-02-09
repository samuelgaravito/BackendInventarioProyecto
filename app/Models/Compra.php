<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Compra extends Model
{
    protected $table = 'compra'; // [cite: 33]

    protected $fillable = [
        'id_acreedor',
        'num_compra',
        'id_forma_pago',
        'monto_total',
        'fecha'
    ]; // [cite: 56, 66, 75, 82, 90]

    public function acreedor(): BelongsTo
    {
        return $this->belongsTo(Acreedor::class, 'id_acreedor'); // [cite: 56]
    }

    public function formaPago(): BelongsTo
    {
        return $this->belongsTo(FormaPago::class, 'id_forma_pago'); // [cite: 75]
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleCompra::class, 'id_compra'); // [cite: 114]
    }

    public function cuentaPagar(): HasOne
    {
        return $this->hasOne(CuentaPagar::class, 'id_compra'); // [cite: 93]
    }
}