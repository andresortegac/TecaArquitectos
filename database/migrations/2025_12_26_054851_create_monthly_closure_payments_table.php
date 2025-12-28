<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monthly_closure_payments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('monthly_closure_id')->index();
            $table->unsignedBigInteger('payment_id')->unique();

            $table->timestamps();

            $table->foreign('monthly_closure_id')->references('id')->on('monthly_closures')->cascadeOnDelete();
            $table->foreign('payment_id')->references('id')->on('payments')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_closure_payments');
    }
};
