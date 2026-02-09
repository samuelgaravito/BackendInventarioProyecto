<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_movimiento', function (Blueprint $table) {
            $table->id(); // pk [cite: 166, 169]
            $table->string('descripcion'); // [cite: 169]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_movimiento');
    }
};