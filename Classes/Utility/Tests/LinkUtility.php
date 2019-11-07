<?php
namespace UniWue\UwA11yCheck\Utility\Tests;

/**
 * Class LinkUtility
 */
class LinkUtility
{
    /**
     * @param \DOMElement $link
     * @return bool
     */
    public static function linkHasImageWithAlt(\DOMElement $link): bool
    {
        $result = false;
        $images = $link->getElementsByTagName('img');

        // If no images present, return false
        if ($images->count() === 0) {
            return false;
        }

        /** @var \DOMElement $image */
        foreach ($images as $image) {
            if ($image->getAttribute('alt') !== '') {
                $result = true;
                break;
            }
        }

        return $result;
    }
}
