<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\TaxHead;
use App\Models\Order;
use App\Models\OrderTaxLine;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderTaxController extends Controller
{
    /**
     * Get available tax options for an order
     */
    public function getTaxOptions(Order $order = null): JsonResponse
    {
        $taxHeads = TaxHead::where('visible_to_client', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'kind', 'percentage', 'visible_to_client']);
            
        return response()->json([
            'success' => true,
            'data' => $taxHeads
        ]);
    }
    
    /**
     * Calculate taxes for an order
     */
    public function calculateTaxes(Request $request, Order $order): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'base_amount' => 'required|numeric|min:0',
            'tax_heads' => 'required|array',
            'tax_heads.*.id' => 'required|exists:tax_heads,id',
            'tax_heads.*.payer' => 'required|in:client,company',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $baseAmount = $request->base_amount;
        $selectedTaxHeads = $request->tax_heads;
        $calculations = [];
        $invoiceTotal = $baseAmount;
        
        foreach ($selectedTaxHeads as $taxData) {
            $taxHead = TaxHead::find($taxData['id']);
            
            if (!$taxHead) {
                continue;
            }
            
            $calculatedAmount = round($baseAmount * ($taxHead->percentage / 100), 2);
            $payer = $taxData['payer'];
            $visible = ($payer === 'client' && $taxHead->visible_to_client);
            
            if ($payer === 'client' && $taxHead->visible_to_client) {
                $invoiceTotal += $calculatedAmount;
            }
            
            $calculations[] = [
                'tax_head_id' => $taxHead->id,
                'tax_head_name' => $taxHead->name,
                'tax_head_code' => $taxHead->code,
                'kind' => $taxHead->kind,
                'percentage' => $taxHead->percentage,
                'base_amount' => $baseAmount,
                'rate' => $taxHead->percentage,
                'calculated_amount' => $calculatedAmount,
                'payer' => $payer,
                'visible' => $visible,
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'base_amount' => $baseAmount,
                'calculations' => $calculations,
                'invoice_total' => $invoiceTotal,
                'total_tax_amount' => array_sum(array_column($calculations, 'calculated_amount')),
                'client_tax_amount' => array_sum(array_filter(array_map(function($calc) {
                    return $calc['payer'] === 'client' ? $calc['calculated_amount'] : 0;
                }, $calculations))),
                'company_tax_amount' => array_sum(array_filter(array_map(function($calc) {
                    return $calc['payer'] === 'company' ? $calc['calculated_amount'] : 0;
                }, $calculations)))
            ]
        ]);
    }
    
    /**
     * Save order with tax information
     */
    public function saveWithTaxes(Request $request, Order $order): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'base_amount' => 'required|numeric|min:0',
            'tax_heads' => 'array',
            'tax_heads.*.id' => 'required|exists:tax_heads,id',
            'tax_heads.*.payer' => 'required|in:client,company',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            DB::beginTransaction();
            
            // Delete existing tax lines for this order
            $order->orderTaxLines()->delete();
            
            $baseAmount = $request->base_amount;
            $selectedTaxHeads = $request->tax_heads ?? [];
            $taxBreakdown = [];
            $invoiceTotal = $baseAmount;
            
            foreach ($selectedTaxHeads as $taxData) {
                $taxHead = TaxHead::find($taxData['id']);
                
                if (!$taxHead) {
                    continue;
                }
                
                $calculatedAmount = round($baseAmount * ($taxHead->percentage / 100), 2);
                $payer = $taxData['payer'];
                $visible = ($payer === 'client' && $taxHead->visible_to_client);
                
                if ($payer === 'client' && $taxHead->visible_to_client) {
                    $invoiceTotal += $calculatedAmount;
                }
                
                // Create order tax line
                OrderTaxLine::create([
                    'order_id' => $order->id,
                    'tax_head_id' => $taxHead->id,
                    'base_amount' => $baseAmount,
                    'rate' => $taxHead->percentage,
                    'calculated_amount' => $calculatedAmount,
                    'payer' => $payer,
                    'visible' => $visible,
                ]);
                
                $taxBreakdown[] = [
                    'tax_head_id' => $taxHead->id,
                    'name' => $taxHead->name,
                    'code' => $taxHead->code,
                    'kind' => $taxHead->kind,
                    'percentage' => $taxHead->percentage,
                    'calculated_amount' => $calculatedAmount,
                    'payer' => $payer,
                    'visible' => $visible,
                ];
            }
            
            // Update order with tax breakdown and new total
            $order->update([
                'tax_breakdown' => json_encode($taxBreakdown),
                'total_amount' => $invoiceTotal,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Order saved with tax information successfully.',
                'data' => [
                    'order' => $order->fresh(['orderTaxLines.taxHead']),
                    'invoice_total' => $invoiceTotal,
                    'tax_breakdown' => $taxBreakdown
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save order with taxes: ' . $e->getMessage()
            ], 500);
        }
    }
}
