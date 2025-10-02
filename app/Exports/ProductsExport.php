<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProductsExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting, ShouldAutoSize
{
    private $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Product::with(['category', 'brand', 'unit'])
            ->select([
                'id', 'name', 'sku', 'barcode', 'description',
                'purchase_price', 'selling_price', 'category_id', 'brand_id', 'unit_id',
                'tax_code', 'tax_rate', 'is_taxable', 'status', 'created_at', 'updated_at'
            ]);

        // Apply filters
        if (!empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (!empty($this->filters['brand_id'])) {
            $query->where('brand_id', $this->filters['brand_id']);
        }

        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'SKU',
            'Barcode',
            'Description',
            'Purchase Price',
            'Selling Price',
            'Category',
            'Brand',
            'Unit',
            'Tax Code',
            'Tax Rate (%)',
            'Is Taxable',
            'Status',
            'Created At',
            'Updated At',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->sku,
            $product->barcode,
            $product->description,
            $product->purchase_price,
            $product->selling_price,
            $product->category?->name ?? '',
            $product->brand?->name ?? '',
            $product->unit?->name ?? '',
            $product->tax_code,
            $product->tax_rate,
            $product->is_taxable ? 'Yes' : 'No',
            $product->status ? 'Active' : 'Inactive',
            $product->created_at?->format('Y-m-d H:i:s'),
            $product->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_00, // Purchase Price
            'G' => NumberFormat::FORMAT_NUMBER_00, // Selling Price
            'L' => NumberFormat::FORMAT_PERCENTAGE_00, // Tax Rate
        ];
    }
}
