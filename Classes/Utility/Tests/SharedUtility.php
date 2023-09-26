<?php

namespace UniWue\UwA11yCheck\Utility\Tests;

use Symfony\Component\DomCrawler\Crawler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SharedUtility
 */
class SharedUtility
{
    public static function elementHasVisibleText(\DOMElement $link): bool
    {
        return StringUtility::stripNewLines($link->textContent);
    }

    public static function elementIsLinkWithHref(\DOMElement $element): bool
    {
        return $element->tagName === 'a' &&  $element->hasAttribute('href');
    }

    public static function elementHasRolePresentation(\DOMElement $element): bool
    {
        return $element->hasAttribute('role') && strtolower($element->getAttribute('role')) === 'presentation';
    }

    public static function elementHasRoleNone(\DOMElement $element): bool
    {
        return $element->hasAttribute('role') && strtolower($element->getAttribute('role')) === 'none';
    }

    public static function elementHasAriaLabelValue(\DOMElement $element): bool
    {
        return $element->hasAttribute('aria-label') && $element->getAttribute('aria-label') !== '';
    }

    public static function elementAriaLabelledByValueExistsAndNotEmpty(\DOMElement $element, Crawler $crawler): bool
    {
        $result = false;
        if (!$element->hasAttribute('aria-labelledby')) {
            return false;
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

    public static function elementHasNonEmptyTitle(\DOMElement $element): bool
    {
        return $element->hasAttribute('title') && $element->getAttribute('title') !== '';
    }

    public static function elementHasAlt(\DOMElement $image): bool
    {
        return $image->hasAttribute('alt');
    }

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

        return mb_strtolower($content) !== mb_strtolower($element->getAttribute('title'));
    }

    public static function elementAttributeValueAllowed(
        \DOMElement $element,
        string $attribute,
        array $allowlist
    ): bool {
        if (!$element->hasAttribute($attribute)) {
            return true;
        }

        $content = StringUtility::clearString($element->getAttribute($attribute));

        return in_array(strtolower($content), $allowlist, true) === false;
    }
}
