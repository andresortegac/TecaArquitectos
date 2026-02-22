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
            if (!Schema::hasColumn('devoluciones_arriendos', 'saldo_devolucion')) {
                $table->decimal('saldo_devolucion', 12, 2)
                    ->default(0.00)
                    ->after('saldo_resultante');
            }
        });
    }

    public function down(): void
    {
        Schema::table('devoluciones_arriendos', function (Blueprint $table) {
            if (Schema::hasColumn('devoluciones_arriendos', 'saldo_devolucion')) {
                $table->dropColumn('saldo_devolucion');
            }
        });
    }
};
