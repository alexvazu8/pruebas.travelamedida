<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tipos_cambio', function (Blueprint $table) {
            $table->id();
            $table->char('moneda_origen', 3); // Ej: 'USD'
            $table->char('moneda_destino', 3); // Ej: 'PYG'
            $table->decimal('tasa_cambio', 18, 6); // 7300.00
            $table->timestamp('fecha_validez');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_cambio');
    }
};
