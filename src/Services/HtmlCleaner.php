<?php

declare(strict_types=1);

namespace App\Services;

final class HtmlCleaner
{

    public static function clean(string $html): string
    {
        // Fix unmatched/broken quotes in attributes
        // Replace problematic quote patterns
        $html = self::replaceQuotes($html);

        // Remove NULL bytes
        $html = self::removeNullBytes($html);

        // Remove BOM
        $html = self::removeBOM($html);

        return $html;
    }

    public static function prepareHtml(string $html): string
    {
        // Handle character encoding
        $html = self::normalizeEncoding($html);

        // Remove problematic content
        $html = self::removeScriptTags($html);
        $html = self::removeStyleTags($html);
        $html = self::removeComments($html);

        // Trim whitespace
        $html = trim($html);

        return $html;
    }


    private static function normalizeEncoding(string $html): string
    {
        // Detect encoding
        $detectedEncoding = mb_detect_encoding(
            $html,
            ['UTF-8', 'ISO-8859-1', 'GBK', 'Windows-1252'],
            true
        );

        if (!$detectedEncoding) {
            $detectedEncoding = 'ISO-8859-1'; // fallback
        }

        // Convert to UTF-8 if needed
        if ($detectedEncoding !== 'UTF-8') {
            $html = mb_convert_encoding($html, 'UTF-8', $detectedEncoding);
        }

        return $html;
    }


    private static function replaceQuotes(string $html): string
    {
        return preg_replace('/="([^"]*)"/s', '="$1"', $html);
    }

    private static function removeNullBytes(string $html): string
    {
        return str_replace("\0", '', $html);
    }

    /**
     * remove Browser object model
     */
    private static function removeBOM(string $html): string
    {
        return preg_replace('/^\xEF\xBB\xBF/', '', $html);
    }

    /**
     * convert the encoding to UTF-8
     */
    private static function finxEncoding(string $html): string
    {
        return mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
    }

    /**
     * Remove script tags and their content
     */
    private static function removeScriptTags(string $html): string
    {
        return preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html);
    }

    /**
     * Remove style tags and their content
     */
    private static function removeStyleTags(string $html): string
    {
        return preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $html);
    }

    /**
     * Remove HTML comments
     */
    private static function removeComments(string $html): string
    {
        return preg_replace('/<!--.*?-->/s', '', $html);
    }
}
