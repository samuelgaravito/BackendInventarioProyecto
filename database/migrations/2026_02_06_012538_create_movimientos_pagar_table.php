<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_pagar', function (Blueprint $table) {
            $table->id(); // pk [cite: 161]
            $table->date('fecha_movimiento'); // [cite: 162]
            $table->decimal('monto_abono', 12, 2); // [cite: 162]
            $table->boolean('estatus')->default(false); // [cite: 162]
            
            // Relación con Cuentas por Pagar (Nivel 4)
            $table->foreignId('id_cuentas_pagar') // [cite: 161]
                  ->constrained('cuentas_pagar')
                  ->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_pagar');
    }
};