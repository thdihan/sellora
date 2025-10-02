<?php

namespace App\Services;

use App\Models\Product;
use App\Models\TaxRule;
use App\Models\TaxRate;
use Illuminate\Support\Collection;

class TaxCalculationService
{
    /**
     * Calculate taxes for a single product.
     */
    public function calculateProductTax(Product $product, float $amount, array $options = []): array
    {
        $applicableRules = $this->getApplicableRules($product, $options);
        
        if ($applicableRules->isEmpty()) {
            return [
                'base_amount' => $amount,
                'tax_amount' => 0,
                'total_amount' => $amount,
                'tax_rate' => 0,
                'rule_applied' => 'No applicable tax rule',
                'tax_breakdown' => []
            ];
        }

        // Use the highest priority rule
        $rule = $applicableRules->first();
        return $rule->calculateTax($amount, $options);
    }

    /**
     * Calculate taxes for multiple line items (order/invoice).
     */
    public function calculateOrderTax(array $lineItems, array $options = []): array
    {
        $totalBase = 0;
        $totalTax = 0;
        $breakdown = [];

        foreach ($lineItems as $item) {
            $product = $item['product'];
            $amount = $item['amount'];
            $quantity = $item['quantity'] ?? 1;

            $lineAmount = $amount * $quantity;
            $taxResult = $this->calculateProductTax($product, $lineAmount, $options);

            $totalBase += $taxResult['base_amount'];
            $totalTax += $taxResult['tax_amount'];

            $breakdown[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'unit_amount' => $amount,
                'line_amount' => $lineAmount,
                'tax_calculation' => $taxResult
            ];
        }

        // Handle shipping tax if applicable
        if (isset($options['shipping_amount']) && $options['shipping_amount'] > 0) {
            $shippingTax = $this->calculateShippingTax($options['shipping_amount'], $options);
            $totalBase += $shippingTax['base_amount'];
            $totalTax += $shippingTax['tax_amount'];
            $breakdown[] = [
                'type' => 'shipping',
                'amount' => $options['shipping_amount'],
                'tax_calculation' => $shippingTax
            ];
        }

        return [
            'subtotal' => $totalBase,
            'total_tax' => $totalTax,
            'total_amount' => $totalBase + $totalTax,
            'line_items' => $breakdown,
            'tax_summary' => $this->generateTaxSummary($breakdown)
        ];
    }

    /**
     * Calculate shipping tax.
     */
    public function calculateShippingTax(float $shippingAmount, array $options = []): array
    {
        $shippingRules = TaxRule::active()
            ->where('applies_to', 'shipping')
            ->byPriority()
            ->get();

        if ($shippingRules->isEmpty()) {
            return [
                'base_amount' => $shippingAmount,
                'tax_amount' => 0,
                'total_amount' => $shippingAmount,
                'tax_rate' => 0,
                'rule_applied' => 'No shipping tax rule'
            ];
        }

        $rule = $shippingRules->first();
        return $rule->calculateTax($shippingAmount, $options);
    }

    /**
     * Get applicable tax rules for a product.
     */
    private function getApplicableRules(Product $product, array $options = []): Collection
    {
        return TaxRule::active()
            ->byPriority()
            ->get()
            ->filter(function ($rule) use ($product) {
                return $rule->appliesTo($product);
            });
    }

    /**
     * Generate tax summary by tax rate.
     */
    private function generateTaxSummary(array $breakdown): array
    {
        $summary = [];

        foreach ($breakdown as $item) {
            $taxCalc = $item['tax_calculation'];
            $taxRate = $taxCalc['tax_rate'];
            $ruleApplied = $taxCalc['rule_applied'];

            $key = $taxRate . '%';
            
            if (!isset($summary[$key])) {
                $summary[$key] = [
                    'tax_rate' => $taxRate,
                    'rule_name' => $ruleApplied,
                    'base_amount' => 0,
                    'tax_amount' => 0
                ];
            }

            $summary[$key]['base_amount'] += $taxCalc['base_amount'];
            $summary[$key]['tax_amount'] += $taxCalc['tax_amount'];
        }

        return array_values($summary);
    }

    /**
     * Validate tax configuration for a product.
     */
    public function validateTaxConfiguration(Product $product): array
    {
        $issues = [];
        $applicableRules = $this->getApplicableRules($product);

        if ($applicableRules->isEmpty()) {
            $issues[] = 'No tax rules apply to this product';
        }

        if ($applicableRules->count() > 1) {
            $priorities = $applicableRules->pluck('priority')->unique();
            if ($priorities->count() < $applicableRules->count()) {
                $issues[] = 'Multiple tax rules with same priority detected';
            }
        }

        foreach ($applicableRules as $rule) {
            if (!$rule->taxRate) {
                $issues[] = "Tax rule '{$rule->name}' has no associated tax rate";
            }

            if ($rule->taxRate && !$rule->taxRate->isEffective()) {
                $issues[] = "Tax rule '{$rule->name}' has an inactive or expired tax rate";
            }
        }

        return [
            'is_valid' => empty($issues),
            'issues' => $issues,
            'applicable_rules' => $applicableRules->count()
        ];
    }
}