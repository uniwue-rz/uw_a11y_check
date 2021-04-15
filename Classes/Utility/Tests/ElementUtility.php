<?php

namespace UniWue\UwA11yCheck\Utility\Tests;

/**
 * Class ElementUtility
 */
class ElementUtility
{
    /**
     * Returns the UID of the element provided by the data-attribute "data-uid"
     *
     * @param \DOMElement $element
     * @return int
     */
    public static function determineElementUid(\DOMElement $element): int
    {
        if (!$element->hasAttribute('data-uid') &&
            $element->parentNode &&
            $element->parentNode instanceof \DOMElement
        ) {
            return self::determineElementUid($element->parentNode);
        }
        if ($element->hasAttribute('data-uid')) {
            return $element->getAttribute('data-uid');
        }
        return 0;
    }

    /**
     * Returns the colPos of the element in the node by looking up for data-colPos.
     * If no colPos is found, null is returned
     *
     * @param \DOMElement $element
     * @return int|null
     */
    public static function determineElementColPos(\DOMElement $element): ?int
    {
        if (!$element->hasAttribute('data-colpos') &&
            $element->parentNode &&
            $element->parentNode instanceof \DOMElement
        ) {
            return self::determineElementColPos($element->parentNode);
        }
        if ($element->hasAttribute('data-colpos')) {
            return (int)$element->getAttribute('data-colpos');
        }
        return null;
    }
}
