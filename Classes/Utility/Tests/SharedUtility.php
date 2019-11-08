<?php
namespace UniWue\UwA11yCheck\Utility\Tests;

use Symfony\Component\DomCrawler\Crawler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SharedUtility
 */
class SharedUtility
{
    /**
     * @param \DOMElement $link
     * @return bool
     */
    public static function elementHasVisibleText(\DOMElement $link): bool
    {
        return $link->textContent !== '';
    }

    /**
     * @param \DOMElement $element
     * @return bool
     */
    public static function elementHasRolePresentation(\DOMElement $element): bool
    {
        return $element->hasAttribute('role') && strtolower($element->getAttribute('role')) === 'presentation';
    }

    /**
     * @param \DOMElement $element
     * @return bool
     */
    public static function elementHasRoleNone(\DOMElement $element): bool
    {
        return $element->hasAttribute('role') && strtolower($element->getAttribute('role')) === 'none';
    }

    /**
     * @param \DOMElement $element
     * @return bool
     */
    public static function elementHasAriaLabelValue(\DOMElement $element): bool
    {
        return $element->hasAttribute('aria-label') && $element->getAttribute('aria-label') !== '';
    }

    /**
     * @param \DOMElement $element
     * @param Crawler $crawler
     * @return bool
     */
    public static function elementAriaLabelledByValueExistsAndNotEmpty(\DOMElement $element, Crawler $crawler): bool
    {
        $result = false;
        if (!$element->hasAttribute('aria-labelledby')) {
            return $result;
        }

        $labelledByValues = $element->getAttribute('aria-labelledby');

        foreach (GeneralUtility::trimExplode(',', $labelledByValues, true) as $labelledByValue) {
            $labelledByElement = $crawler->filter('#' . $labelledByValue)->first();
            if ($labelledByElement->count() > 0 && $labelledByElement->text() !== '') {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * @param \DOMElement $element
     * @return bool
     */
    public static function elementHasNonEmptyTitle(\DOMElement $element): bool
    {
        return $element->hasAttribute('title') && $element->getAttribute('title') !== '';
    }

    /**
     * @param \DOMElement $image
     * @return bool
     */
    public static function elementHasAlt(\DOMElement $image): bool
    {
        return $image->hasAttribute('alt');
    }

    /**
     * @param \DOMElement $element
     * @return bool
     */
    public static function elementTitleNotRedundant(\DOMElement $element): bool
    {
        if (($element->tagName === 'a' && $element->textContent === '') ||
            ($element->tagName === 'img' && !$element->hasAttribute('alt')) ||
            ($element->tagName === 'a' && !$element->hasAttribute('title')) ||
            ($element->tagName === 'img' && !$element->hasAttribute('title'))
        ) {
            return true;
        }

        if ($element->tagName === 'a') {
            $content = $element->textContent;
        } else {
            $content = $element->getAttribute('alt');
        }

        return strtolower($content) !== strtolower($element->getAttribute('title'));
    }
}
