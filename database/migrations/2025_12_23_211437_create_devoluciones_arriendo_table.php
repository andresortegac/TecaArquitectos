<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('devoluciones_arriendos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('arriendo_id')->constrained('arriendos')->cascadeOnDelete();

            $table->date('fecha_devolucion');
            $table->integer('cantidad_devuelta');

            $table->integer('dias_transcurridos');
            $table->integer('domingos_desc');
            $table->integer('dias_lluvia_desc');
            $table->integer('dias_cobrables');

            $table->decimal('tarifa_diaria', 12, 2)->default(0);

            $table->decimal('total_alquiler', 12, 2)->default(0);
            $table->decimal('total_merma', 12, 2)->default(0);
            $table->decimal('total_cobrado', 12, 2)->default(0);

            $table->decimal('pago_recibido', 12, 2)->default(0);

            $table->integer('cantidad_restante')->default(0);
            $table->decimal('saldo_resultante', 12, 2)->default(0);

            $table->string('descripcion_incidencia')->nullable();
            $table->text('nota')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devoluciones_arriendos');
    }
};
