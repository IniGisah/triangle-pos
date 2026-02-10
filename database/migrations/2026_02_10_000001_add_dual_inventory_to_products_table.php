<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDualInventoryToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Add dual inventory tracking columns
            $table->integer('wholesale_unit_stock')->nullable()->default(0)->after('wholesale_price')
                ->comment('Number of wholesale units (boxes, cartons, etc.) in stock');
            $table->integer('retail_unit_stock')->default(0)->after('wholesale_unit_stock')
                ->comment('Number of retail units (pieces, items, etc.) in stock');
            $table->integer('retail_price')->nullable()->after('product_price')
                ->comment('Price per retail unit (piece, item, etc.)');
            $table->integer('wholesale_quantity')->nullable()->after('retail_price')
                ->comment('Number of retail units contained in one wholesale unit');
            $table->varchar('retail_unit')->nullable()->after('wholesale_quantity')
                ->comment('Unit of measurement for retail units (e.g., piece, item)');
            // Add indexes for performance
            $table->index(['wholesale_unit_stock', 'retail_unit_stock'], 'idx_dual_inventory');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_dual_inventory');
            $table->dropColumn(['wholesale_unit_stock', 'retail_unit_stock', 'retail_price']);
        });
    }
}
