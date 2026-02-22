<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('devoluciones_arriendos', function (Blueprint $table) {
            // ✅ solo agregamos lo que faltaba
            if (!Schema::hasColumn('devoluciones_arriendos', 'costo_transporte')) {
                $table->decimal('costo_transporte', 12, 2)->default(0)->after('total_merma');
            }

            if (!Schema::hasColumn('devoluciones_arriendos', 'detalle_transporte')) {
                $table->string('detalle_transporte', 255)->nullable()->after('transporte_herramientas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('devoluciones_arriendos', function (Blueprint $table) {
            if (Schema::hasColumn('devoluciones_arriendos', 'costo_transporte')) {
                $table->dropColumn('costo_transporte');
            }
            if (Schema::hasColumn('devoluciones_arriendos', 'detalle_transporte')) {
                $table->dropColumn('detalle_transporte');
            }
        });
    }
};
