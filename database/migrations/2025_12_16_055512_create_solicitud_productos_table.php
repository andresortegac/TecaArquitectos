<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('solicitud_productos', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();

            $table->foreignId('solicitud_id')
                  ->constrained('solicitudes')
                  ->cascadeOnDelete();

            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->cascadeOnDelete();

            $table->integer('cantidad_solicitada');
            $table->integer('cantidad_aprobada')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_productos');
    }
};
