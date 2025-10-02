<?php

/**
 * Due Calculation Service
 *
 * This service handles comprehensive due calculations for customers,
 * including aging analysis, payment history, and credit management.
 *
 * @category Services
 * @package  App\Services
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Bill;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Due Calculation Service
 *
 * Provides comprehensive due calculation functionality including
 * aging analysis, payment tracking, and financial summaries.
 */
class DueCalculationService
{
    /**
     * Calculate comprehensive due information for a customer
     *
     * @param Customer $customer The customer instance
     * @return array Comprehensive due calculation data
     */
    public function calculateCustomerDues(Customer $customer): array
    {
        $orders = $customer->orders()->get();
        $bills = $customer->bills()->get();
        
        return [
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'total_outstanding' => $this->calculateTotalOutstanding($customer),
            'aging_analysis' => $this->calculateAgingAnalysis($customer),
            'payment_history' => $this->getPaymentHistory($customer),
            'credit_summary' => $this->getCreditSummary($customer),
            'recent_transactions' => $this->getRecentTransactions($customer),
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Calculate total outstanding amount for a customer
     *
     * @param Customer $customer The customer instance
     * @return float Total outstanding amount
     */
    public function calculateTotalOutstanding(Customer $customer): float
    {
        $unpaidOrders = $customer->orders()
            ->whereNotIn('status', ['paid', 'cancelled', 'refunded'])
            ->sum('total_amount');
            
        $unpaidBills = $customer->bills()
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->sum('amount');
            
        return (float) ($unpaidOrders + $unpaidBills);
    }

    /**
     * Calculate aging analysis for customer dues
     *
     * @param Customer $customer The customer instance
     * @return array Aging analysis breakdown
     */
    public function calculateAgingAnalysis(Customer $customer): array
    {
        $now = Carbon::now();
        
        $aging = [
            'current' => 0,      // 0-30 days
            '30_days' => 0,      // 31-60 days
            '60_days' => 0,      // 61-90 days
            '90_days' => 0,      // 91-120 days
            'over_120' => 0,     // Over 120 days
        ];
        
        // Analyze unpaid orders
        $unpaidOrders = $customer->orders()
            ->whereNotIn('status', ['paid', 'cancelled', 'refunded'])
            ->get();
            
        foreach ($unpaidOrders as $order) {
            $daysDue = $now->diffInDays(Carbon::parse($order->created_at));
            $amount = (float) $order->total_amount;
            
            if ($daysDue <= 30) {
                $aging['current'] += $amount;
            } elseif ($daysDue <= 60) {
                $aging['30_days'] += $amount;
            } elseif ($daysDue <= 90) {
                $aging['60_days'] += $amount;
            } elseif ($daysDue <= 120) {
                $aging['90_days'] += $amount;
            } else {
                $aging['over_120'] += $amount;
            }
        }
        
        // Analyze unpaid bills
        $unpaidBills = $customer->bills()
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->get();
            
        foreach ($unpaidBills as $bill) {
            $daysDue = $now->diffInDays(Carbon::parse($bill->created_at));
            $amount = (float) $bill->amount;
            
            if ($daysDue <= 30) {
                $aging['current'] += $amount;
            } elseif ($daysDue <= 60) {
                $aging['30_days'] += $amount;
            } elseif ($daysDue <= 90) {
                $aging['60_days'] += $amount;
            } elseif ($daysDue <= 120) {
                $aging['90_days'] += $amount;
            } else {
                $aging['over_120'] += $amount;
            }
        }
        
        return $aging;
    }

    /**
     * Get payment history summary for a customer
     *
     * @param Customer $customer The customer instance
     * @return array Payment history data
     */
    public function getPaymentHistory(Customer $customer): array
    {
        $last6Months = Carbon::now()->subMonths(6);
        
        $paidOrders = $customer->orders()
            ->where('status', 'paid')
            ->where('updated_at', '>=', $last6Months)
            ->sum('total_amount');
            
        $paidBills = $customer->bills()
            ->where('status', 'paid')
            ->where('updated_at', '>=', $last6Months)
            ->sum('amount');
            
        $totalPaid = (float) ($paidOrders + $paidBills);
        
        $paymentCount = $customer->orders()
            ->where('status', 'paid')
            ->where('updated_at', '>=', $last6Months)
            ->count() + 
            $customer->bills()
            ->where('status', 'paid')
            ->where('updated_at', '>=', $last6Months)
            ->count();
            
        return [
            'total_paid_6_months' => $totalPaid,
            'payment_count_6_months' => $paymentCount,
            'average_payment' => $paymentCount > 0 ? $totalPaid / $paymentCount : 0,
            'last_payment_date' => $this->getLastPaymentDate($customer),
        ];
    }

    /**
     * Get credit summary for a customer
     *
     * @param Customer $customer The customer instance
     * @return array Credit summary data
     */
    public function getCreditSummary(Customer $customer): array
    {
        $totalOutstanding = $this->calculateTotalOutstanding($customer);
        $creditLimit = $customer->credit_limit ?? 0;
        
        return [
            'credit_limit' => (float) $creditLimit,
            'credit_used' => $totalOutstanding,
            'credit_available' => max(0, $creditLimit - $totalOutstanding),
            'credit_utilization_percentage' => $creditLimit > 0 ? 
                round(($totalOutstanding / $creditLimit) * 100, 2) : 0,
            'is_over_limit' => $totalOutstanding > $creditLimit,
        ];
    }

    /**
     * Get recent transactions for a customer
     *
     * @param Customer $customer The customer instance
     * @param int $limit Number of transactions to return
     * @return array Recent transactions
     */
    public function getRecentTransactions(Customer $customer, int $limit = 10): array
    {
        $transactions = collect();
        
        // Get recent orders
        $recentOrders = $customer->orders()
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'type' => 'order',
                    'amount' => (float) $order->total_amount,
                    'status' => $order->status,
                    'date' => $order->created_at->toISOString(),
                    'description' => "Order #{$order->id}",
                ];
            });
            
        // Get recent bills
        $recentBills = $customer->bills()
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($bill) {
                return [
                    'id' => $bill->id,
                    'type' => 'bill',
                    'amount' => (float) $bill->amount,
                    'status' => $bill->status,
                    'date' => $bill->created_at->toISOString(),
                    'description' => $bill->purpose ?? "Bill #{$bill->id}",
                ];
            });
            
        $transactions = $recentOrders->concat($recentBills)
            ->sortByDesc('date')
            ->take($limit)
            ->values();
            
        return $transactions->toArray();
    }

    /**
     * Get the last payment date for a customer
     *
     * @param Customer $customer The customer instance
     * @return string|null Last payment date
     */
    private function getLastPaymentDate(Customer $customer): ?string
    {
        $lastOrderPayment = $customer->orders()
            ->where('status', 'paid')
            ->latest('updated_at')
            ->first();
            
        $lastBillPayment = $customer->bills()
            ->where('status', 'paid')
            ->latest('updated_at')
            ->first();
            
        $lastPaymentDate = null;
        
        if ($lastOrderPayment && $lastBillPayment) {
            $lastPaymentDate = $lastOrderPayment->updated_at->gt($lastBillPayment->updated_at) 
                ? $lastOrderPayment->updated_at 
                : $lastBillPayment->updated_at;
        } elseif ($lastOrderPayment) {
            $lastPaymentDate = $lastOrderPayment->updated_at;
        } elseif ($lastBillPayment) {
            $lastPaymentDate = $lastBillPayment->updated_at;
        }
        
        return $lastPaymentDate ? $lastPaymentDate->toISOString() : null;
    }

    /**
     * Calculate due summary for multiple customers
     *
     * @param array $customerIds Array of customer IDs
     * @return array Summary data for multiple customers
     */
    public function calculateBulkDueSummary(array $customerIds): array
    {
        $customers = Customer::whereIn('id', $customerIds)->get();
        $summary = [];
        
        foreach ($customers as $customer) {
            $summary[] = [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'total_outstanding' => $this->calculateTotalOutstanding($customer),
                'aging_summary' => $this->getAgingSummary($customer),
            ];
        }
        
        return $summary;
    }

    /**
     * Get a simplified aging summary
     *
     * @param Customer $customer The customer instance
     * @return array Simplified aging summary
     */
    private function getAgingSummary(Customer $customer): array
    {
        $aging = $this->calculateAgingAnalysis($customer);
        
        return [
            'current_and_30' => $aging['current'] + $aging['30_days'],
            'over_60' => $aging['60_days'] + $aging['90_days'] + $aging['over_120'],
            'total' => array_sum($aging),
        ];
    }
}