<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_cobrar', function (Blueprint $table) {
            $table->id(); // pk
            $table->date('fecha'); //
            $table->decimal('monto_deuda', 12, 2); //
            $table->decimal('monto_abono', 12, 2); //
            $table->decimal('saldo', 12, 2); //
            $table->boolean('estatus')->default(false); //
            
            // Relación con Cuentas por Cobrar (Nivel 4)
            $table->foreignId('id_cuentas_cobrar') //
                  ->constrained('cuentas_cobrar')
                  ->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_cobrar');
    }
};