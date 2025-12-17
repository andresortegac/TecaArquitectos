<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');
            $table->string('categorias')->nullable();
            $table->integer('cantidad')->default(0);
            $table->decimal('costo', 12, 2)->default(0);
            $table->string('ubicacion')->nullable(); // estante, rack, etc
            $table->enum('estado', ['disponible','dañado','reservado'])->default('disponible');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
