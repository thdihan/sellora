<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductBrand;
use App\Models\ProductUnit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductsImport implements ToCollection, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    private $importedCount = 0;
    private $errors = [];
    private $updateExisting;

    public function __construct($updateExisting = false)
    {
        $this->updateExisting = $updateExisting;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                $this->processRow($row->toArray(), $index + 2); // +2 for header and 0-based index
                $this->importedCount++;
            } catch (\Exception $e) {
                $this->errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }
    }

    private function processRow(array $row, int $rowNumber)
    {
        // Clean and validate data
        $data = $this->cleanRowData($row);
        
        // Validate required fields
        $validator = Validator::make($data, $this->rowRules());
        
        if ($validator->fails()) {
            throw new \Exception("Validation failed: " . implode(', ', $validator->errors()->all()));
        }

        // Find or create related models
        $category = $this->findOrCreateCategory($data['category'] ?? null);
        $brand = $this->findOrCreateBrand($data['brand'] ?? null);
        $unit = $this->findOrCreateUnit($data['unit'] ?? null);

        // Prepare product data
        $productData = [
            'name' => $data['name'],
            'sku' => $data['sku'],
            'barcode' => $data['barcode'] ?? null,
            'description' => $data['description'] ?? null,
            'purchase_price' => $data['purchase_price'] ?? 0,
            'selling_price' => $data['selling_price'] ?? 0,
            'category_id' => $category?->id,
            'brand_id' => $brand?->id,
            'unit_id' => $unit?->id,
            'tax_code' => $data['tax_code'] ?? null,
            'tax_rate' => $data['tax_rate'] ?? 0,
            'is_taxable' => $data['is_taxable'] ?? false,
            'status' => $data['status'] ?? true,
        ];

        // Create or update product
        if ($this->updateExisting && !empty($data['sku'])) {
            Product::updateOrCreate(
                ['sku' => $data['sku']],
                $productData
            );
        } else {
            // Check for duplicate SKU
            if (!empty($data['sku']) && Product::where('sku', $data['sku'])->exists()) {
                throw new \Exception("Product with SKU '{$data['sku']}' already exists");
            }
            
            Product::create($productData);
        }
    }

    private function cleanRowData(array $row): array
    {
        return [
            'name' => trim($row['name'] ?? $row['product_name'] ?? ''),
            'sku' => trim($row['sku'] ?? $row['product_sku'] ?? ''),
            'barcode' => trim($row['barcode'] ?? $row['product_barcode'] ?? ''),
            'description' => trim($row['description'] ?? $row['product_description'] ?? ''),
            'selling_price' => $this->parseNumeric($row['price'] ?? $row['selling_price'] ?? $row['sale_price'] ?? $row['cost_price'] ?? 0),
            'category' => trim($row['category'] ?? $row['product_category'] ?? ''),
            'brand' => trim($row['brand'] ?? $row['product_brand'] ?? ''),
            'unit' => trim($row['unit'] ?? $row['product_unit'] ?? ''),
            'tax_code' => trim($row['tax_code'] ?? ''),
            'tax_rate' => $this->parseNumeric($row['tax_rate'] ?? 0),
            'is_taxable' => $this->parseBoolean($row['is_taxable'] ?? $row['taxable'] ?? false),
            'status' => $this->parseBoolean($row['status'] ?? $row['active'] ?? true),
        ];
    }

    private function parseNumeric($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        
        // Remove currency symbols and spaces
        $cleaned = preg_replace('/[^0-9.,]/', '', $value);
        return (float) str_replace(',', '', $cleaned);
    }

    private function parseBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        
        $value = strtolower(trim($value));
        return in_array($value, ['1', 'true', 'yes', 'y', 'active']);
    }

    private function findOrCreateCategory(?string $name): ?ProductCategory
    {
        if (empty($name)) {
            return null;
        }
        
        return ProductCategory::firstOrCreate(
            ['name' => $name],
            ['description' => "Auto-created from import"]
        );
    }

    private function findOrCreateBrand(?string $name): ?ProductBrand
    {
        if (empty($name)) {
            return null;
        }
        
        return ProductBrand::firstOrCreate(
            ['name' => $name],
            ['description' => "Auto-created from import"]
        );
    }

    private function findOrCreateUnit(?string $name): ?ProductUnit
    {
        if (empty($name)) {
            return null;
        }
        
        return ProductUnit::firstOrCreate(
            ['name' => $name],
            ['short_name' => strtoupper(substr($name, 0, 3))]
        );
    }

    private function rowRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100',
            'selling_price' => 'required|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function rules(): array
    {
        return [];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
