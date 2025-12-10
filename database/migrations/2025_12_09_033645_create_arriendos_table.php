<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('arriendos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');

            $table->integer('cantidad')->default(1);

            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();

            $table->decimal('precio_total', 12, 2)->default(0);

            $table->enum('estado', ['activo','devuelto','vencido'])->default('activo');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arriendos');
    }
};

