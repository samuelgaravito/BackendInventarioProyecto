<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id(); // pk [cite: 4, 5]
            $table->string('nombre'); // [cite: 7]
            $table->integer('cedula')->unique(); // [cite: 8]
            $table->string('telefono')->nullable(); // [cite: 9]
            $table->string('direccion'); // [cite: 10]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};