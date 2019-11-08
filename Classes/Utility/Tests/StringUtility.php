<?php
namespace UniWue\UwA11yCheck\Utility\Tests;

/**
 * Class StringUtility
 */
class StringUtility
{
    /**
     * Removes a fixed set of special chars from the given string
     *
     * @param string $string
     * @return string
     */
    public static function clearString(string $string): string
    {
        $remove = ['.', '#', '{', '}', '[', ']'];
        return str_replace($remove, '', $string);
    }
}
