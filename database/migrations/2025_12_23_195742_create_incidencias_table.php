<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arriendo_id')->constrained('arriendos')->cascadeOnDelete();
            $table->string('tipo');              // LLUVIA, MERMA, etc.
            $table->integer('dias')->default(0);
            $table->decimal('costo', 12, 2)->default(0);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};

