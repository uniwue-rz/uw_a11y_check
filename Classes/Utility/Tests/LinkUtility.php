<?php
namespace UniWue\UwA11yCheck\Utility\Tests;

/**
 * Class LinkUtility
 */
class LinkUtility
{
    /**
     * @param \DOMElement $element
     * @return bool
     */
    public static function linkHasImageWithAlt(\DOMElement $element): bool
    {
        $result = false;
        $images = $element->getElementsByTagName('img');

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

    /**
     * @param \DOMElement $element
     * @param array $blacklist
     * @return bool
     */
    public static function linkTextNotBlacklisted(\DOMElement $element, array $blacklist): bool
    {
        if (empty($blacklist) || $element->textContent === '') {
            return true;
        }
        $content = StringUtility::clearString($element->textContent);

        return in_array(strtolower($content), $blacklist, true) === false;
    }

    /**
     * @param \DOMElement $element
     * @param string $attribute
     * @param array $blacklist
     * @return bool
     */
    public static function linkImageAttributeNotBlacklisted(
        \DOMElement $element,
        string $attribute,
        array $blacklist
    ): bool {
        if (empty($blacklist) || $attribute === '') {
            return true;
        }

        $images = $element->getElementsByTagName('img');

        // If no images present, return false
        if ($images->count() === 0) {
            return true;
        }

        /** @var \DOMElement $image */
        foreach ($images as $image) {
            if (!SharedUtility::elementAttributeValueNotBlacklisted($image, $attribute, $blacklist)) {
                return false;
                break;
            }
        }

        return true;
    }
}
