<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {

   

        Schema::create('arriendos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->integer('cantidad')->default(1);
            $table->date('fecha_inicio');
            $table->date('fecha_entrega')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->enum('estado', ['activo','devuelto','vencido'])->default('activo');
            $table->string('cerrado');
            $table->decimal('precio_total', 12, 2)->default(0);
            $table->integer('dias_transcurridos')->default(0);
            $table->integer('domingos_desc')->default(0);
            $table->integer('dias_lluvia_desc')->default(0);
            $table->integer('dias_cobrables')->default(0);
            $table->decimal('total_alquiler', 12, 2)->default(0);
            $table->decimal('total_merma', 12, 2)->default(0);
            $table->decimal('total_pagado', 12, 2)->default(0);
            $table->decimal('saldo', 12, 2)->default(0);
            $table->integer('dias_mora')->default(0);
            $table->string('semaforo_pago');

    

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arriendos');
    }
};

