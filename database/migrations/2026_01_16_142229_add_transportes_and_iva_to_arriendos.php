<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Tabla para múltiples transportes
        Schema::create('arriendo_transportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arriendo_id')
                ->constrained('arriendos')
                ->onDelete('cascade');

            $table->enum('tipo', ['entrega','recogida','ambos','otro'])->default('entrega');
            $table->dateTime('fecha')->nullable();
            $table->decimal('valor', 12, 2)->default(0);
            $table->string('nota', 255)->nullable();

            $table->timestamps();
        });

        // 2) IVA en tabla arriendos (se decide al cerrar)
        Schema::table('arriendos', function (Blueprint $table) {
            if (!Schema::hasColumn('arriendos', 'iva_aplica')) {
                $table->boolean('iva_aplica')->default(false)->after('saldo');
            }
            if (!Schema::hasColumn('arriendos', 'iva_rate')) {
                $table->decimal('iva_rate', 5, 2)->default(0.19)->after('iva_aplica');
            }
        });
    }

    public function down(): void
    {
        Schema::table('arriendos', function (Blueprint $table) {
            if (Schema::hasColumn('arriendos', 'iva_rate')) $table->dropColumn('iva_rate');
            if (Schema::hasColumn('arriendos', 'iva_aplica')) $table->dropColumn('iva_aplica');
        });

        Schema::dropIfExists('arriendo_transportes');
    }
};
