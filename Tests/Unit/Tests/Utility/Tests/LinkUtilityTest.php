<?php

namespace UniWue\UwA11yCheck\Tests\Unit\Tests\Internal;

/*
 * This file is part of the Extension "uw_a11y_check" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use DOMDocument;
use TYPO3\TestingFramework\Core\BaseTestCase;
use UniWue\UwA11yCheck\Utility\Tests\LinkUtility;

/**
 * Test cases for LinkUtilityTest
 */
class LinkUtilityTest extends BaseTestCase
{
    public function linkHasImageWithAltTestsDataProvider(): array
    {
        return [
            'link with no image' => [
                '<a href=#">Just Text</a>',
                false,
            ],
            'link with image but no alt' => [
                '<a href=#"><img src="test.gif" /></a>',
                false,
            ],
            'link with image and empty alt' => [
                '<a href=#"><img src="test.gif" alt=""/></a>',
                false,
            ],
            'link with image and alt' => [
                '<a href=#"><img src="test.gif" alt="Alternative"/></a>',
                true,
            ],
            'link with multiple images and at least one alt' => [
                '<a href=#"><img src="test.gif" alt=""/><img src="test.gif" alt="Alternative"/></a>',
                true,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider linkHasImageWithAltTestsDataProvider
     */
    public function linkHasImageWithAltTests(string $html, bool $expected): void
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('a')->item(0);
        $result = LinkUtility::linkHasImageWithAlt($element);

        self::assertEquals($expected, $result);
    }

    public function linkTextNotBlacklistedTestsDataProvider(): array
    {
        return [
            'text not blacklisted' => [
                '<a href="link.html">This is a link</a>',
                ['more', 'details'],
                true,
            ],
            'text blacklisted exact case' => [
                '<a href="link.html">more</a>',
                ['more', 'details'],
                false,
            ],
            'text blacklisted different case' => [
                '<a href="link.html">Details</a>',
                ['more', 'details'],
                false,
            ],
            'only chars taken into account' => [
                '<a href="link.html">Details...</a>',
                ['more', 'details'],
                false,
            ],
            'empty blacklist' => [
                '<a href="link.html">Details...</a>',
                [],
                true,
            ],
            'empty test' => [
                '<a href="link.html"><img src="test.gif" alt="" /></a>',
                ['more', 'details'],
                true,
            ],
        ];
    }

    /**
     * @dataProvider linkTextNotBlacklistedTestsDataProvider
     * @test
     */
    public function linkTextNotBlacklistedTests(string $html, array $blacklist, bool $expected)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('a')->item(0);
        $result = LinkUtility::linkTextAllowed($element, $blacklist);

        self::assertEquals($expected, $result);
    }

    public function linkImageAttributeNotBlacklistedTestsDataProvider(): array
    {
        return [
            'no blacklist' => [
                '<a href="link.html" title="more">This is a link</a>',
                'title',
                [],
                true,
            ],
            'no image' => [
                '<a href="link.html" title="more">This is a link</a>',
                'title',
                ['more', 'details'],
                true,
            ],
            'image with no attribute' => [
                '<a href="link.html" title="more"><img src="test.gif" /></a>',
                'title',
                ['more', 'details'],
                true,
            ],
            'image with attribute value not blacklisted' => [
                '<a href="link.html" title="more"><img src="test.gif" title="Not blacklisted" /></a>',
                'title',
                ['more', 'details'],
                true,
            ],
            'image with attribute value blacklisted' => [
                '<a href="link.html" title="more"><img src="test.gif" title="Details" /></a>',
                'title',
                ['more', 'details'],
                false,
            ],
            'image with attribute value blacklisted including extra chars' => [
                '<a href="link.html" title="more"><img src="test.gif" title="Details..." /></a>',
                'title',
                ['more', 'details'],
                false,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider linkImageAttributeNotBlacklistedTestsDataProvider
     */
    public function linkImageAttributeNotBlacklistedTests(
        string $html,
        string $attribute,
        array $blacklist,
        bool $expected
    ): void {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('a')->item(0);
        $result = LinkUtility::linkImageAttributeAllowed($element, $attribute, $blacklist);

        self::assertEquals($expected, $result);
    }

    public function hasRedundantLinkNamesTestsDataProvider(): array
    {
        return [
            'no links at all' => [
                [
                    '<img src="test.html" alt="" />',
                ],
                0,
            ],
            'only one link' => [
                [
                    '<a href="link.html">Link 1</a>',
                ],
                0,
            ],
            'two links but not redundant name' => [
                [
                    '<a href="link.html">Link 1</a>',
                    '<a href="link.html">Link 2</a>',
                ],
                0,
            ],
            'two links with redundant name' => [
                [
                    '<a href="link.html">Link 1</a>',
                    '<a href="link.html">Link 1</a>',
                ],
                1,
            ],
            'three links with redundant name' => [
                [
                    '<a href="link.html">Link 1</a>',
                    '<a href="link.html">Link 1</a>',
                    '<a href="link.html">Link 1</a>',
                ],
                1,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider hasRedundantLinkNamesTestsDataProvider
     */
    public function hasRedundantLinkNamesTests(array $htmlArray, int $expected): void
    {
        $elements = [];

        foreach ($htmlArray as $htmlLink) {
            $doc = new DOMDocument();
            $doc->loadHTML($htmlLink);
            $element = $doc->getElementsByTagName('a')->item(0);
            $elements[] = $element;
        }

        /** @var \DOMElement $element */
        $result = LinkUtility::getRedundantLinkNames($elements);

        self::assertCount($expected, $result);
    }
}
