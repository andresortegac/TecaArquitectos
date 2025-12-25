<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devoluciones_arriendos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('arriendo_id')
                ->constrained('arriendos')
                ->cascadeOnDelete();

            $table->foreignId('arriendo_item_id')
                ->constrained('arriendo_items')
                ->cascadeOnDelete();

            $table->date('fecha_devolucion');

            $table->integer('cantidad_devuelta')->default(0);
            $table->integer('dias_transcurridos')->default(0);

            $table->integer('domingos_desc')->default(0);
            $table->integer('dias_lluvia_desc')->default(0);
            $table->integer('dias_cobrables')->default(0);

            $table->decimal('tarifa_diaria', 12, 2);

            $table->decimal('total_alquiler', 14, 2)->default(0);
            $table->decimal('total_merma', 14, 2)->default(0);
            $table->decimal('total_cobrado', 14, 2)->default(0);

            $table->decimal('pago_recibido', 14, 2)->default(0);

            $table->integer('cantidad_restante')->default(0);
            $table->decimal('saldo_resultante', 14, 2)->default(0);

            $table->text('descripcion_incidencia')->nullable();
            $table->text('nota')->nullable();

            $table->timestamps();

            // Índices opcionales útiles para consultas
            $table->index(['arriendo_id', 'arriendo_item_id']);
            $table->index('fecha_devolucion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devoluciones_arriendos');
    }
};