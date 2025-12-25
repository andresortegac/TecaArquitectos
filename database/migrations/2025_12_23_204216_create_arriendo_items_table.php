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
        Schema::create('arriendo_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('arriendo_id')
                ->constrained('arriendos')
                ->cascadeOnDelete();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->cascadeOnDelete();

            $table->decimal('tarifa_diaria', 12, 2);

            $table->integer('cantidad_inicial');
            $table->integer('cantidad_actual');

            $table->date('fecha_inicio_item');
            $table->date('fecha_fin_item')->nullable();

            $table->boolean('cerrado')->default(false);
            $table->string('estado', 50)->default('activo');

            $table->decimal('precio_total', 14, 2)->default(0);
            $table->decimal('total_alquiler', 14, 2)->default(0);
            $table->decimal('total_merma', 14, 2)->default(0);
            $table->decimal('total_pagado', 14, 2)->default(0);
            $table->decimal('saldo', 14, 2)->default(0);

            $table->timestamps();

            // Índices opcionales
            $table->index(['arriendo_id', 'producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arriendo_items');
    }
};