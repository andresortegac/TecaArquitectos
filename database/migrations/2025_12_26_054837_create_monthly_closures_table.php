<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monthly_closures', function (Blueprint $table) {
            $table->id();

            // Mes contable (primer día del mes, ej: 2025-12-01)
            $table->date('month_start')->unique();

            // último día del mes (ej: 2025-12-31)
            $table->date('month_end');

            $table->dateTime('closed_at')->index();
            $table->unsignedBigInteger('total_amount');

            $table->json('method_breakdown')->nullable();

            $table->unsignedBigInteger('closed_by')->nullable(); // null = system
            $table->timestamps();

            $table->foreign('closed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_closures');
    }
};
