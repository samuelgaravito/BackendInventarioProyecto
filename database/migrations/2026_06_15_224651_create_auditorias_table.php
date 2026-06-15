<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            // id_usuario es nulleable por si ocurre una acción antes de loguearse (como un intento fallido)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('descripcion');
            $table->string('ip', 45); // 45 caracteres para soportar IPv4 e IPv6
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};