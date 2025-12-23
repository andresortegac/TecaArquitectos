<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();

            $table->integer('stock_minimo')->default(10);
            $table->boolean('alerta_stock')->default(true);
            $table->string('mes_defecto')->default('Enero');
            $table->boolean('bloquear_sin_stock')->default(true);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('configuraciones');
    }
};

