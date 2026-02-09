<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->string('sale_unit')->nullable()->after('quantity');
            $table->integer('sale_unit_multiplier')->default(1)->after('sale_unit');
            $table->integer('base_quantity')->default(0)->after('sale_unit_multiplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropColumn(['sale_unit', 'sale_unit_multiplier', 'base_quantity']);
        });
    }
};
