<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('arriendos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->cascadeOnDelete();

            $table->foreignId('producto_id')
                ->nullable()
                ->constrained('productos')
                ->nullOnDelete();

            $table->integer('cantidad')->default(1);

            $table->foreignId('obra_id')
                ->constrained('obras')
                ->cascadeOnDelete();

            $table->date('fecha_inicio');
            $table->date('fecha_entrega')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->date('fecha_devolucion_real')->nullable();

            $table->enum('estado', ['activo', 'devuelto', 'vencido'])
                ->default('activo');

            $table->boolean('cerrado')->default(false);

            $table->decimal('precio_total', 14, 2)->default(0);
            $table->integer('dias_transcurridos')->default(0);
            $table->integer('domingos_desc')->default(0);
            $table->integer('dias_lluvia_desc')->default(0);
            $table->integer('dias_cobrables')->default(0);

            $table->decimal('total_alquiler', 14, 2)->default(0);
            $table->decimal('total_merma', 14, 2)->default(0);
            $table->decimal('total_pagado', 14, 2)->default(0);
            $table->decimal('saldo', 14, 2)->default(0);

            $table->integer('dias_mora')->default(0);
            $table->string('semaforo_pago')->default('VERDE');

            $table->timestamps();

            // índices útiles
            $table->index(['cliente_id', 'producto_id']);
            $table->index('estado');
            $table->index('fecha_inicio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arriendos');
    }
};
