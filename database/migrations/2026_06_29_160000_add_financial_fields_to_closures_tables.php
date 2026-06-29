<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('daily_closures', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_closures', 'total_gastos')) {
                $table->unsignedBigInteger('total_gastos')->default(0)->after('total_amount');
            }

            if (!Schema::hasColumn('daily_closures', 'utilidad')) {
                $table->bigInteger('utilidad')->default(0)->after('total_gastos');
            }

            if (!Schema::hasColumn('daily_closures', 'observacion')) {
                $table->text('observacion')->nullable()->after('closed_by');
            }
        });

        Schema::table('monthly_closures', function (Blueprint $table) {
            if (!Schema::hasColumn('monthly_closures', 'total_gastos')) {
                $table->unsignedBigInteger('total_gastos')->default(0)->after('total_amount');
            }

            if (!Schema::hasColumn('monthly_closures', 'utilidad')) {
                $table->bigInteger('utilidad')->default(0)->after('total_gastos');
            }

            if (!Schema::hasColumn('monthly_closures', 'observacion')) {
                $table->text('observacion')->nullable()->after('closed_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('daily_closures', function (Blueprint $table) {
            if (Schema::hasColumn('daily_closures', 'observacion')) {
                $table->dropColumn('observacion');
            }

            if (Schema::hasColumn('daily_closures', 'utilidad')) {
                $table->dropColumn('utilidad');
            }

            if (Schema::hasColumn('daily_closures', 'total_gastos')) {
                $table->dropColumn('total_gastos');
            }
        });

        Schema::table('monthly_closures', function (Blueprint $table) {
            if (Schema::hasColumn('monthly_closures', 'observacion')) {
                $table->dropColumn('observacion');
            }

            if (Schema::hasColumn('monthly_closures', 'utilidad')) {
                $table->dropColumn('utilidad');
            }

            if (Schema::hasColumn('monthly_closures', 'total_gastos')) {
                $table->dropColumn('total_gastos');
            }
        });
    }
};
