<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obras', function (Blueprint $table) {
        $table->id();
        $table->string('direccion');
        $table->text('detalle')->nullable();

        $table->foreignId('cliente_id')
            ->constrained('clientes')
            ->cascadeOnDelete(); // o restrictOnDelete() / nullOnDelete()

        $table->timestamps();
    });

    }

    public function down(): void
    {
        Schema::dropIfExists('obras');
    }
};
