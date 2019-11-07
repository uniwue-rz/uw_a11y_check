<?php
namespace UniWue\UwA11yCheck\Utility\Tests;

/**
 * Class ElementUidUtility
 */
class ElementUidUtility
{
    /**
     * Returns the UID of the element provided by the data-attribute "data-uid"
     *
     * @param \DOMElement $element
     * @return int
     */
    public static function determineElementUid(\DOMElement $element): int
    {
        if (!$element->hasAttribute('data-uid') && $element->parentNode) {
            return self::determineElementUid($element->parentNode);
        } elseif ($element->hasAttribute('data-uid')) {
            return $element->getAttribute('data-uid');
        } else {
            return 0;
        }
    }
}
