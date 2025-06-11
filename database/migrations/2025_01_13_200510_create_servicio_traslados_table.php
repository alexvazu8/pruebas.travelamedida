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
        Schema::create('servicio_traslados', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_servicio_transfer', 100);
            $table->unsignedBigInteger('zona_origen_id');
            $table->unsignedBigInteger('zona_destino_id');
            $table->foreign('zona_origen_id')->references('Id_Zona')->on('zonas')->onDelete('cascade');
            $table->foreign('zona_destino_id')->references('Id_Zona')->on('zonas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicio_traslados');
    }
};
