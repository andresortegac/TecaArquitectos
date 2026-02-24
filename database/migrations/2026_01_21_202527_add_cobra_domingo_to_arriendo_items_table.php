<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('arriendo_items', function (Blueprint $table) {
            $table->boolean('cobra_domingo')->default(false)->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('arriendo_items', function (Blueprint $table) {
            $table->dropColumn('cobra_domingo');
        });
    }
};
