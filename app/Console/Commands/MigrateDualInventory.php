<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Product\Entities\Product;

class MigrateDualInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:migrate-dual-unit {--dry-run : Preview changes without applying them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing product inventory from single quantity to dual inventory (wholesale + retail units)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('Running in DRY RUN mode - no changes will be saved.');
        }

        $this->info('Starting dual inventory migration...');
        $this->newLine();

        $products = Product::all();
        $totalProducts = $products->count();
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        $this->info("Found {$totalProducts} products to process.");
        $this->newLine();

        $bar = $this->output->createProgressBar($totalProducts);
        $bar->start();

        foreach ($products as $product) {
            try {
                $currentQty = $product->product_quantity ?? 0;
                $wholesaleQty = $product->wholesale_quantity ?? 0;

                // Skip if dual inventory already set
                if ($product->wholesale_unit_stock !== null || $product->retail_unit_stock !== null) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Existing quantity/unit is wholesale (boxes); treat product_quantity as boxes
                $wholesaleStock = $currentQty;
                $retailStock = 0;

                $logEntry = [
                    'product_id' => $product->id,
                    'product_name' => $product->product_name,
                    'product_code' => $product->product_code,
                    'old_quantity' => $currentQty,
                    'wholesale_quantity_per_unit' => $wholesaleQty,
                    'new_wholesale_stock' => $wholesaleStock,
                    'new_retail_stock' => $retailStock,
                    'total_after' => ($wholesaleStock * $wholesaleQty) + $retailStock
                ];

                if (!$isDryRun) {
                    DB::transaction(function () use ($product, $wholesaleStock, $retailStock) {
                        $product->update([
                            'wholesale_unit_stock' => $wholesaleStock,
                            'retail_unit_stock' => $retailStock
                        ]);
                    });

                    Log::channel('single')->info('Dual inventory migration', $logEntry);
                } else {
                    // In dry run, just display
                    if ($this->output->isVerbose()) {
                        $this->newLine();
                        $this->line("Product: {$product->product_name} (#{$product->id})");
                        $this->line("  Current: {$currentQty} {$product->product_unit}");
                        if ($wholesaleQty > 0) {
                            $this->line("  Split into: {$wholesaleStock} {$product->wholesale_unit} + {$retailStock} {$product->product_unit}");
                        } else {
                            $this->line("  Migrated to: {$retailStock} {$product->product_unit} (no wholesale)");
                        }
                    }
                }

                $updated++;
            } catch (\Exception $e) {
                $errors++;
                Log::channel('single')->error('Dual inventory migration error', [
                    'product_id' => $product->id,
                    'error' => $e->getMessage()
                ]);
                
                if ($this->output->isVerbose()) {
                    $this->newLine();
                    $this->error("Error processing product #{$product->id}: " . $e->getMessage());
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Display summary
        $this->info('Migration Complete!');
        $this->newLine();
        $this->table(
            ['Status', 'Count'],
            [
                ['Total Products', $totalProducts],
                ['Successfully Migrated', $updated],
                ['Skipped (Already Migrated)', $skipped],
                ['Errors', $errors],
            ]
        );

        if ($isDryRun) {
            $this->newLine();
            $this->warn('This was a DRY RUN - no changes were saved to the database.');
            $this->info('Run without --dry-run to apply changes.');
        } else {
            $this->newLine();
            $this->info('Migration logs saved to storage/logs/laravel.log');
        }

        return Command::SUCCESS;
    }
}
