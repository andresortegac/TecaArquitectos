<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devoluciones_arriendos', function (Blueprint $table) {
            if (!Schema::hasColumn('devoluciones_arriendos', 'cantidad_danada')) {
                $table->integer('cantidad_danada')->default(0)->after('cantidad_devuelta');
            }
        });
    }

    public function down(): void
    {
        Schema::table('devoluciones_arriendos', function (Blueprint $table) {
            if (Schema::hasColumn('devoluciones_arriendos', 'cantidad_danada')) {
                $table->dropColumn('cantidad_danada');
            }
        });
    }
};
