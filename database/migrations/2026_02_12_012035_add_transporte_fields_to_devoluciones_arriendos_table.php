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
    Schema::table('devoluciones_arriendos', function (Blueprint $table) {

        if (!Schema::hasColumn('devoluciones_arriendos', 'transporte_herramientas')) {
            $table->string('transporte_herramientas', 40)->nullable()->after('descripcion_incidencia');
        }

        if (!Schema::hasColumn('devoluciones_arriendos', 'detalle_transporte')) {
            $table->string('detalle_transporte', 255)->nullable()->after('transporte_herramientas');
        }

        if (!Schema::hasColumn('devoluciones_arriendos', 'costo_transporte')) {
            $table->decimal('costo_transporte', 12, 2)->default(0)->after('total_merma');
        }
    });
}

};
