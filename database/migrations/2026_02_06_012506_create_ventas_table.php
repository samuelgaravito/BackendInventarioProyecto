<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id(); // pk [cite: 39]
            $table->string('num_venta'); // [cite: 65]
            $table->date('fecha'); // [cite: 87, 88]
            $table->decimal('monto_total', 12, 2); // [cite: 81]
            
            // Relaciones
            $table->foreignId('id_cliente')->constrained('clientes'); // [cite: 55]
            $table->foreignId('id_forma_pago')->constrained('formas_pago'); // [cite: 74]
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};