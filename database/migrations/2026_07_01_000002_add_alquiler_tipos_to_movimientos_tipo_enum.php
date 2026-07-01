<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE movimientos MODIFY tipo ENUM('ingreso','salida','ajuste_positivo','ajuste_negativo','fuera_servicio','producto_alquilado','producto_devuelto') NOT NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE movimientos MODIFY tipo ENUM('ingreso','salida','ajuste_positivo','ajuste_negativo','fuera_servicio') NOT NULL");
    }
};
