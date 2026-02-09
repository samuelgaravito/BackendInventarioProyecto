<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_pagar', function (Blueprint $table) {
            $table->id(); // pk [cite: 77]
            $table->date('fecha'); // [cite: 87]
            $table->string('descripcion')->nullable(); // [cite: 124]
            $table->decimal('deuda_total', 12, 2); // [cite: 124]
            $table->decimal('deuda_actual', 12, 2); // [cite: 124]
            $table->boolean('estatus')->default(false); // [cite: 124]
            
            // Relaciones
            $table->foreignId('id_acreedor')->constrained('acreedores'); // [cite: 84]
            $table->foreignId('id_compra')->constrained('compras'); // [cite: 93]
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_pagar');
    }
};