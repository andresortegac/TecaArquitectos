<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_closures', function (Blueprint $table) {
            $table->id();

            // Fecha calendario cerrada (ej: 2025-12-26)
            $table->date('business_date')->unique();

            // Hora real en la que se cerró (tú: 7:00pm)
            $table->dateTime('closed_at')->index();

            // Total confirmado del día
            $table->unsignedBigInteger('total_amount');

            // Opcional: resumen por método para ver caja rápido sin recalcular
            // Ej: {"efectivo":100000,"nequi":50000,...}
            $table->json('method_breakdown')->nullable();

            // Quién cerró (null = system)
            $table->unsignedBigInteger('closed_by')->nullable();

            $table->timestamps();

            $table->foreign('closed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_closures');
    }
};
