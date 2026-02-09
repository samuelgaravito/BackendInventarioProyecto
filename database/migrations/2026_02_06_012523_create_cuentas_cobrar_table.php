<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_cobrar', function (Blueprint $table) {
            $table->id(); // pk [cite: 37]
            $table->date('fecha_emision'); // [cite: 72]
            $table->date('fecha_vencimiento'); // [cite: 80]
            $table->string('plazo_pago'); // [cite: 89]
            $table->decimal('monto', 12, 2); // [cite: 97]
            $table->boolean('estatus')->default(false); // false = pendiente, true = pagado [cite: 101]
            
            // Relaciones
            $table->foreignId('id_cliente')->constrained('clientes'); // [cite: 51]
            $table->foreignId('id_venta')->constrained('ventas'); // [cite: 63]
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_cobrar');
    }
};