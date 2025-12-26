<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('solicitud_id') // arriendo_id
                ->constrained('arriendos')
                ->cascadeOnDelete();

            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->cascadeOnDelete();

            $table->foreignId('obra_id')
                ->constrained('obras')
                ->cascadeOnDelete();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->cascadeOnDelete();

            $table->integer('cantidad_solicitada');
            $table->integer('cantidad_aprobada')->default(0);

            $table->enum('estado', ['aprobado', 'rechazado'])
                ->default('rechazado');

            $table->dateTime('fecha_aprobado')->nullable();

            $table->timestamps();

            $table->index(['solicitud_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
