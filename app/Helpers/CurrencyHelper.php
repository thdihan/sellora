<?php

/**
 * Currency Helper for BTD (৳) formatting
 *
 * PHP version 8.1
 *
 * @category Helper
 * @package  App\Helpers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Helpers;

/**
 * Currency Helper for BTD (৳) formatting
 * 
 * Provides utility functions for formatting Bangladeshi Taka (BTD) currency
 * with proper locale formatting and symbol placement.
 *
 * @category Helper
 * @package  App\Helpers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class CurrencyHelper
{
    /**
     * Format amount as BTD currency
     * 
     * @param float|int|string $amount The amount to format
     *
     * @return string Formatted currency string like "৳12,345.00"
     */
    public static function formatBTD($amount): string
    {
        // Convert to float and ensure 2 decimal places
        $numericAmount = (float) $amount;
        
        // Format with thousands separator and 2 decimal places
        $formatted = number_format($numericAmount, 2, '.', ',');
        
        // Return with BTD symbol
        return '৳' . $formatted;
    }
    
    /**
     * Format amount as BTD currency without decimal places for whole numbers
     * 
     * @param  float|int|string $amount The amount to format
     *
     * @return string Formatted currency string
     */
    public static function formatBTDShort($amount): string
    {
        $numericAmount = (float) $amount;
        
        // If it's a whole number, don't show decimals
        if ($numericAmount == floor($numericAmount)) {
            $formatted = number_format($numericAmount, 0, '.', ',');
        } else {
            $formatted = number_format($numericAmount, 2, '.', ',');
        }
        
        return '৳' . $formatted;
    }
    
    /**
     * Parse BTD formatted string back to numeric value
     * 
     * @param string $formattedAmount BTD formatted string like "৳12,345.00"
     *
     * @return float Numeric value
     */
    public static function parseBTD(string $formattedAmount): float
    {
        // Remove BTD symbol and commas
        $cleaned = str_replace(['৳', ','], '', $formattedAmount);
        
        return (float) $cleaned;
    }
    
    /**
     * Get the BTD currency symbol
     * 
     * @return string The BTD symbol
     */
    public static function getSymbol(): string
    {
        return '৳';
    }
}