<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('devoluciones_arriendos', function (Blueprint $table) {
            if (!Schema::hasColumn('devoluciones_arriendos', 'arriendo_item_id')) {
                $table->foreignId('arriendo_item_id')
                    ->nullable()
                    ->after('arriendo_id')
                    ->constrained('arriendo_items')
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('devoluciones_arriendos', function (Blueprint $table) {
            if (Schema::hasColumn('devoluciones_arriendos', 'arriendo_item_id')) {
                $table->dropConstrainedForeignId('arriendo_item_id');
            }
        });
    }
};
