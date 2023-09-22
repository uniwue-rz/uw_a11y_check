<?php

namespace UniWue\UwA11yCheck\Utility\Tests;

/**
 * Class HeaderUtility
 */
class HeaderUtility
{
    /**
     * Returns, if the given current header ins a descend of the given parent header
     */
    public static function headersSequentiallyDescending(?\DOMElement $previousHeader, \DOMElement $currentHeader): bool
    {
        $result = false;
        if (!$previousHeader) {
            return false;
        }

        $previousHeaderNumeric = (int)str_replace('h', '', strtolower($previousHeader->tagName));
        $nextHeaderNumeric = (int)str_replace('h', '', strtolower($currentHeader->tagName));

        if ($nextHeaderNumeric <= $previousHeaderNumeric ||
            ($nextHeaderNumeric > $previousHeaderNumeric &&
                $nextHeaderNumeric === ($previousHeaderNumeric + 1))
        ) {
            $result = true;
        }

        return $result;
    }
}
