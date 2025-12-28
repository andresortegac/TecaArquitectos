<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_parts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('payment_id')->index();

            // Métodos definidos por ti
            $table->enum('method', ['efectivo', 'nequi', 'daviplata', 'transferencia'])->index();

            $table->unsignedBigInteger('amount');

            // opcional: referencia de transferencia, número de comprobante, etc.
            $table->string('reference', 120)->nullable();

            $table->timestamps();

            $table->foreign('payment_id')->references('id')->on('payments')->cascadeOnDelete();

            $table->index(['payment_id', 'method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_parts');
    }
};
