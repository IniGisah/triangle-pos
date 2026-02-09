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
        Schema::table('products', function (Blueprint $table) {
            $table->string('wholesale_unit')->nullable()->after('product_unit');
            $table->integer('wholesale_quantity')->nullable()->after('wholesale_unit');
            $table->integer('wholesale_price')->nullable()->after('wholesale_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['wholesale_unit', 'wholesale_quantity', 'wholesale_price']);
        });
    }
};
