<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\StockSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncProductData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:sync 
                            {--product= : Sync specific product by ID}
                            {--dry-run : Show what would be synced without making changes}
                            {--force : Force sync even if no discrepancies found}
                            {--recalculate : Recalculate stock from transactions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize product data across all modules (price, stock, inventory)';

    protected StockSyncService $stockSyncService;

    /**
     * Create a new command instance.
     */
    public function __construct(StockSyncService $stockSyncService)
    {
        parent::__construct();
        $this->stockSyncService = $stockSyncService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting product data synchronization...');
        
        $isDryRun = $this->option('dry-run');
        $productId = $this->option('product');
        $force = $this->option('force');
        $recalculate = $this->option('recalculate');
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }
        
        try {
            if ($productId) {
                return $this->syncSingleProduct($productId, $isDryRun, $force, $recalculate);
            } else {
                return $this->syncAllProducts($isDryRun, $force, $recalculate);
            }
        } catch (\Exception $e) {
            $this->error('Synchronization failed: ' . $e->getMessage());
            Log::error('Product sync command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Sync a single product.
     */
    private function syncSingleProduct(int $productId, bool $isDryRun, bool $force, bool $recalculate): int
    {
        $product = Product::find($productId);
        if (!$product) {
            $this->error("Product with ID {$productId} not found.");
            return 1;
        }
        
        $this->info("Syncing product: {$product->name} (SKU: {$product->sku})");
        
        if ($recalculate) {
            return $this->recalculateProduct($product, $isDryRun);
        }
        
        return $this->syncProduct($product, $isDryRun, $force);
    }

    /**
     * Sync all products.
     */
    private function syncAllProducts(bool $isDryRun, bool $force, bool $recalculate): int
    {
        if ($recalculate) {
            return $this->recalculateAllProducts($isDryRun);
        }
        
        // First, check for discrepancies
        $this->info('Checking for stock discrepancies...');
        $discrepancies = $this->stockSyncService->getStockDiscrepancies();
        
        if ($discrepancies->isEmpty() && !$force) {
            $this->info('No stock discrepancies found. Use --force to sync anyway.');
            return 0;
        }
        
        $this->info("Found {$discrepancies->count()} products with discrepancies.");
        
        if ($isDryRun) {
            $this->showDiscrepancies($discrepancies);
            return 0;
        }
        
        // Perform synchronization
        $this->info('Starting synchronization...');
        $results = $this->stockSyncService->syncAllProducts();
        
        $this->displayResults($results);
        
        return $results['errors'] > 0 ? 1 : 0;
    }

    /**
     * Sync individual product.
     */
    private function syncProduct(Product $product, bool $isDryRun, bool $force): int
    {
        if ($isDryRun) {
            $discrepancies = $this->stockSyncService->getStockDiscrepancies()
                ->where('product.id', $product->id);
            
            if ($discrepancies->isEmpty() && !$force) {
                $this->info('No discrepancies found for this product.');
                return 0;
            }
            
            $this->showDiscrepancies($discrepancies);
            return 0;
        }
        
        $result = $this->stockSyncService->syncProductStock($product);
        
        if ($result['synced']) {
            $this->info("✓ Synced: {$result['product_sku']} (Difference: {$result['difference']})");
        } else {
            $this->line("- Skipped: {$result['product_sku']} ({$result['reason']})");
        }
        
        return 0;
    }

    /**
     * Recalculate product stock from transactions.
     */
    private function recalculateProduct(Product $product, bool $isDryRun): int
    {
        $this->info("Recalculating stock for: {$product->name} (SKU: {$product->sku})");
        
        if ($isDryRun) {
            $this->warn('DRY RUN: Would recalculate stock from transactions');
            return 0;
        }
        
        $result = $this->stockSyncService->recalculateFromTransactions($product);
        
        if ($result['updated']) {
            $this->info("✓ Recalculated: Previous={$result['previous_balance']}, New={$result['calculated_balance']}");
        } else {
            $this->line('- No changes needed');
        }
        
        return 0;
    }

    /**
     * Recalculate all products from transactions.
     */
    private function recalculateAllProducts(bool $isDryRun): int
    {
        $products = Product::all();
        $this->info("Recalculating stock for {$products->count()} products...");
        
        if ($isDryRun) {
            $this->warn('DRY RUN: Would recalculate all product stocks from transactions');
            return 0;
        }
        
        $updated = 0;
        $errors = 0;
        
        $progressBar = $this->output->createProgressBar($products->count());
        $progressBar->start();
        
        foreach ($products as $product) {
            try {
                $result = $this->stockSyncService->recalculateFromTransactions($product);
                if ($result['updated']) {
                    $updated++;
                }
            } catch (\Exception $e) {
                $errors++;
                Log::error('Failed to recalculate product stock', [
                    'product_id' => $product->id,
                    'error' => $e->getMessage()
                ]);
            }
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        
        $this->info("Recalculation complete: {$updated} updated, {$errors} errors");
        
        return $errors > 0 ? 1 : 0;
    }

    /**
     * Show discrepancies in a table format.
     */
    private function showDiscrepancies($discrepancies): void
    {
        if ($discrepancies->isEmpty()) {
            $this->info('No discrepancies found.');
            return;
        }
        
        $headers = ['Product ID', 'SKU', 'Name', 'Product Stock', 'Balance Stock', 'Difference'];
        $rows = [];
        
        foreach ($discrepancies as $item) {
            $product = $item['product'];
            $rows[] = [
                $product->id,
                $product->sku,
                substr($product->name, 0, 30) . (strlen($product->name) > 30 ? '...' : ''),
                $item['product_stock'],
                $item['balance_stock'],
                $item['difference']
            ];
        }
        
        $this->table($headers, $rows);
    }

    /**
     * Display synchronization results.
     */
    private function displayResults(array $results): void
    {
        $this->newLine();
        $this->info('Synchronization Results:');
        $this->line("✓ Synced: {$results['synced']}");
        $this->line("- Skipped: {$results['skipped']}");
        
        if ($results['errors'] > 0) {
            $this->error("✗ Errors: {$results['errors']}");
        }
        
        if ($this->option('verbose')) {
            $this->newLine();
            $this->info('Detailed Results:');
            
            foreach ($results['details'] as $detail) {
                if (isset($detail['error'])) {
                    $this->error("✗ {$detail['product_sku']}: {$detail['error']}");
                } elseif ($detail['synced']) {
                    $this->info("✓ {$detail['product_sku']}: Difference {$detail['difference']}");
                } else {
                    $this->line("- {$detail['product_sku']}: {$detail['reason']}");
                }
            }
        }
    }
}