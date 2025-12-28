<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Fecha/hora REAL del pago (lo que necesitas para reportes con precisión)
            $table->dateTime('occurred_at')->index();

            // Día calendario "contable" (para filtrar rápido por día/mes sin recalcular)
            // = date(occurred_at)
            $table->date('business_date')->index();

            // Total del pago (suma de payment_parts)
            $table->unsignedBigInteger('total_amount');

            // Estado del pago (solo confirmed suma en reportes)
            $table->enum('status', ['draft', 'confirmed', 'cancelled'])->default('confirmed')->index();

            $table->dateTime('confirmed_at')->nullable();
            $table->unsignedBigInteger('confirmed_by')->nullable();

            $table->dateTime('cancelled_at')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->string('cancel_reason', 255)->nullable();

            // Origen del pago (devolución, factura/arriendo, etc.)
            // Usa morph: source_type + source_id para amarrarlo a tu modelo (Return/Arriendo/etc.)
            $table->string('source_type')->nullable()->index();
            $table->unsignedBigInteger('source_id')->nullable()->index();

            // Si quieres atar cliente/obra/arriendo (si existen en tu BD)
            $table->unsignedBigInteger('client_id')->nullable()->index();
            $table->unsignedBigInteger('obra_id')->nullable()->index();
            $table->unsignedBigInteger('arriendo_id')->nullable()->index();

            // Notas opcionales
            $table->text('note')->nullable();

            $table->timestamps();

            // Si tienes users:
            $table->foreign('confirmed_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('cancelled_by')->references('id')->on('users')->nullOnDelete();

            // Si existen estas tablas, descomenta:
            // $table->foreign('client_id')->references('id')->on('clients')->nullOnDelete();
            // $table->foreign('obra_id')->references('id')->on('obras')->nullOnDelete();
            // $table->foreign('arriendo_id')->references('id')->on('arriendos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
