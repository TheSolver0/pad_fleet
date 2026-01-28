<?php

if (!function_exists('format_money')) {
    /**
     * Format a number as money (French: space thousands, comma decimal).
     */
    function format_money(?float $value, int $decimals = 0): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        return number_format((float) $value, $decimals, ',', ' ');
    }
}

if (!function_exists('format_number')) {
    /**
     * Format a number with French separators (space thousands, comma decimal).
     */
    function format_number(?float $value, int $decimals = 0): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        return number_format((float) $value, $decimals, ',', ' ');
    }
}

if (!function_exists('parse_french_number')) {
    /**
     * Parse a French-formatted number string (spaces, comma decimal) to numeric string for DB/validation.
     */
    function parse_french_number(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        $cleaned = preg_replace('/\s+/', '', trim($value));
        $cleaned = str_replace(',', '.', $cleaned);
        if ($cleaned === '' || $cleaned === '.') {
            return null;
        }
        if (!is_numeric($cleaned)) {
            return null;
        }
        return $cleaned;
    }
}
