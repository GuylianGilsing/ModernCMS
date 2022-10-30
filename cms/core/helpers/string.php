<?php

declare(strict_types=1);

namespace ModernCMS\Core\Helpers\String;

/**
 * Checks if a string contains text, and isn't empty or contains only whitespace.
 */
function string_contains_text(string $text): bool
{
    $targetWhitespaceRegex = '[\\n\\r\s\\t]+';
    $removedWhitespaceString = preg_replace('~'.$targetWhitespaceRegex.'~', '', $text);

    return strlen($removedWhitespaceString) > 0;
}
