<?php

namespace UniWue\UwA11yCheck\Utility\Tests;

/**
 * Class StringUtility
 */
class StringUtility
{
    /**
     * Removes a fixed set of special chars from the given string
     */
    public static function clearString(string $string): string
    {
        $remove = ['.', '#', '{', '}', '[', ']'];
        return str_replace($remove, '', $string);
    }

    /**
     * Removes all new lines from the given string
     */
    public static function stripNewLines(?string $text): string
    {
        return trim(preg_replace('/\s\s+/', ' ', $text));
    }
}
