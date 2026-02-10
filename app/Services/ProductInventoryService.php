<?php

namespace App\Services;

use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductInventoryService
{
    /**
     * Deduct stock from product inventory with automatic box breaking
     *
     * @param Product $product
     * @param int $quantity
     * @param string $unit ('retail' or 'wholesale')
     * @return array Stock breakdown after deduction
     * @throws Exception
     */
    public function deductStock(Product $product, int $quantity, string $unit = 'retail'): array
    {
        return DB::transaction(function () use ($product, $quantity, $unit) {
            // Refresh product to get latest stock
            $product->refresh();

            if ($unit === 'wholesale') {
                return $this->deductWholesaleStock($product, $quantity);
            }

            return $this->deductRetailStock($product, $quantity);
        });
    }

    /**
     * Deduct wholesale units (boxes) from stock
     *
     * @param Product $product
     * @param int $quantity Number of boxes to deduct
     * @return array
     * @throws Exception
     */
    protected function deductWholesaleStock(Product $product, int $quantity): array
    {
        $wholesaleStock = $product->wholesale_unit_stock ?? 0;

        if ($wholesaleStock < $quantity) {
            throw new Exception("Insufficient wholesale stock. Available: {$wholesaleStock} {$product->wholesale_unit}, Required: {$quantity} {$product->wholesale_unit}");
        }

        $product->update([
            'wholesale_unit_stock' => $wholesaleStock - $quantity
        ]);

        Log::info("Deducted wholesale stock", [
            'product_id' => $product->id,
            'product_name' => $product->product_name,
            'quantity' => $quantity,
            'unit' => $product->wholesale_unit,
            'remaining_wholesale' => $product->wholesale_unit_stock,
            'remaining_retail' => $product->retail_unit_stock
        ]);

        return $this->getStockBreakdown($product->fresh());
    }

    /**
     * Deduct retail units (pieces) from stock with automatic box breaking
     *
     * @param Product $product
     * @param int $quantity Number of pieces to deduct
     * @return array
     * @throws Exception
     */
    protected function deductRetailStock(Product $product, int $quantity): array
    {
        $retailStock = $product->retail_unit_stock ?? 0;
        $wholesaleStock = $product->wholesale_unit_stock ?? 0;
        $wholesaleQuantity = $product->wholesale_quantity ?? 0;

        // Calculate total available pieces
        $totalAvailable = $retailStock + ($wholesaleStock * $wholesaleQuantity);

        if ($totalAvailable < $quantity) {
            throw new Exception("Insufficient stock. Available: {$totalAvailable} {$product->product_unit}, Required: {$quantity} {$product->product_unit}");
        }

        $remainingToDeduct = $quantity;
        $boxesBroken = 0;

        // First, deduct from available retail stock
        if ($retailStock > 0) {
            $deductFromRetail = min($retailStock, $remainingToDeduct);
            $retailStock -= $deductFromRetail;
            $remainingToDeduct -= $deductFromRetail;
        }

        // If still need more, break boxes into pieces
        while ($remainingToDeduct > 0 && $wholesaleStock > 0 && $wholesaleQuantity > 0) {
            // Break one box
            $wholesaleStock--;
            $retailStock += $wholesaleQuantity;
            $boxesBroken++;

            // Deduct from newly broken box
            $deductFromNewlyBroken = min($retailStock, $remainingToDeduct);
            $retailStock -= $deductFromNewlyBroken;
            $remainingToDeduct -= $deductFromNewlyBroken;
        }

        // Update product
        $product->update([
            'wholesale_unit_stock' => $wholesaleStock,
            'retail_unit_stock' => $retailStock
        ]);

        Log::info("Deducted retail stock", [
            'product_id' => $product->id,
            'product_name' => $product->product_name,
            'quantity' => $quantity,
            'unit' => $product->product_unit,
            'boxes_broken' => $boxesBroken,
            'remaining_wholesale' => $product->wholesale_unit_stock,
            'remaining_retail' => $product->retail_unit_stock
        ]);

        return $this->getStockBreakdown($product->fresh());
    }

    /**
     * Add stock to product inventory
     *
     * @param Product $product
     * @param int $quantity
     * @param string $unit ('retail' or 'wholesale')
     * @return array Stock breakdown after addition
     */
    public function addStock(Product $product, int $quantity, string $unit = 'retail'): array
    {
        return DB::transaction(function () use ($product, $quantity, $unit) {
            $product->refresh();

            if ($unit === 'wholesale') {
                $product->update([
                    'wholesale_unit_stock' => ($product->wholesale_unit_stock ?? 0) + $quantity
                ]);
            } else {
                $product->update([
                    'retail_unit_stock' => ($product->retail_unit_stock ?? 0) + $quantity
                ]);
            }

            Log::info("Added stock", [
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'quantity' => $quantity,
                'unit' => $unit,
                'remaining_wholesale' => $product->wholesale_unit_stock,
                'remaining_retail' => $product->retail_unit_stock
            ]);

            return $this->getStockBreakdown($product->fresh());
        });
    }

    /**
     * Get stock breakdown with boxes, pieces, and total
     *
     * @param Product $product
     * @return array
     */
    public function getStockBreakdown(Product $product): array
    {
        $wholesaleStock = $product->wholesale_unit_stock ?? 0;
        $retailStock = $product->retail_unit_stock ?? 0;
        $wholesaleQuantity = $product->wholesale_quantity ?? 0;

        $totalInBaseUnits = ($wholesaleStock * $wholesaleQuantity) + $retailStock;

        // When legacy data uses product_unit as wholesale, use pcs as retail label by default
        $retailUnitLabel = $product->retail_unit ?? __('sale::sale.retail_unit_default');

        return [
            'wholesale_stock' => $wholesaleStock,
            'wholesale_unit' => $product->wholesale_unit ?? ($product->product_unit ?? ''),
            'retail_stock' => $retailStock,
            'retail_unit' => $retailUnitLabel,
            'total_quantity' => $totalInBaseUnits,
            'formatted' => $this->formatStockDisplay($product, $wholesaleStock, $retailStock, $totalInBaseUnits, $retailUnitLabel)
        ];
    }

    /**
     * Format stock display string
     *
     * @param Product $product
     * @param int $wholesaleStock
     * @param int $retailStock
     * @param int $total
     * @return string
     */
    public function formatStockDisplay(Product $product, int $wholesaleStock, int $retailStock, int $total, string $retailUnitLabel): string
    {
        $parts = [];

        // Add wholesale units if available and configured
        $wholesaleUnitLabel = $product->wholesale_unit ?? ($product->product_unit ?? '');
        if ($wholesaleStock > 0 && $wholesaleUnitLabel) {
            $parts[] = "{$wholesaleStock} {$wholesaleUnitLabel}";
        }

        // Add retail units if available or if no wholesale units
        if ($retailStock > 0 || empty($parts)) {
            $parts[] = "{$retailStock} {$retailUnitLabel}";
        }

        $display = implode(' + ', $parts);

        // Add total in parentheses if we have both types
        if (count($parts) > 1) {
            $display .= " ({$total} {$retailUnitLabel} total)";
        }

        return $display;
    }

    /**
     * Break one wholesale unit into retail units
     *
     * @param Product $product
     * @return bool Success status
     */
    public function breakWholesaleUnit(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            $product->refresh();

            $wholesaleStock = $product->wholesale_unit_stock ?? 0;
            $wholesaleQuantity = $product->wholesale_quantity ?? 0;

            if ($wholesaleStock < 1 || $wholesaleQuantity < 1) {
                return false;
            }

            $product->update([
                'wholesale_unit_stock' => $wholesaleStock - 1,
                'retail_unit_stock' => ($product->retail_unit_stock ?? 0) + $wholesaleQuantity
            ]);

            Log::info("Manually broke wholesale unit", [
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'pieces_added' => $wholesaleQuantity
            ]);

            return true;
        });
    }

    /**
     * Validate if sufficient stock is available
     *
     * @param Product $product
     * @param int $quantity
     * @param string $unit
     * @return bool
     */
    public function hasEnoughStock(Product $product, int $quantity, string $unit = 'retail'): bool
    {
        if ($unit === 'wholesale') {
            return ($product->wholesale_unit_stock ?? 0) >= $quantity;
        }

        // For retail, check total available (loose pieces + pieces in boxes)
        $retailStock = $product->retail_unit_stock ?? 0;
        $wholesaleStock = $product->wholesale_unit_stock ?? 0;
        $wholesaleQuantity = $product->wholesale_quantity ?? 0;
        $totalAvailable = $retailStock + ($wholesaleStock * $wholesaleQuantity);

        return $totalAvailable >= $quantity;
    }
}
