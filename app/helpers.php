<?php

/**
 * Global helper functions
 *
 * PHP version 8.1
 *
 * @category Helper
 * @package  App
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

use App\Helpers\CurrencyHelper;

if (!function_exists('formatBTD')) {
    /**
     * Format amount as BTD currency
     *
     * @param float|int|string $amount The amount to format
     *
     * @return string Formatted currency string like "৳12,345.00"
     */
    function formatBTD($amount): string
    {
        return CurrencyHelper::formatBTD($amount);
    }
}

if (!function_exists('formatBTDShort')) {
    /**
     * Format amount as BTD currency without decimals for whole numbers
     *
     * @param float|int|string $amount The amount to format
     *
     * @return string Formatted currency string
     */
    function formatBTDShort($amount): string
    {
        return CurrencyHelper::formatBTDShort($amount);
    }
}

if (!function_exists('parseBTD')) {
    /**
     * Parse BTD formatted string back to numeric value
     *
     * @param string $formattedAmount BTD formatted string like "৳12,345.00"
     *
     * @return float Numeric value
     */
    function parseBTD(string $formattedAmount): float
    {
        return CurrencyHelper::parseBTD($formattedAmount);
    }
}

if (!function_exists('btdSymbol')) {
    /**
     * Get the BTD currency symbol
     *
     * @return string The BTD symbol
     */
    function btdSymbol(): string
    {
        return CurrencyHelper::getSymbol();
    }
}