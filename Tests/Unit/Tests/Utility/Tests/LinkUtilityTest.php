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
    /**
     * @return array
     */
    public function linkHasImageWithAltTestsDataProvider()
    {
        return [
            'link with no image' => [
                '<a href=#">Just Text</a>',
                false
            ],
            'link with image but no alt' => [
                '<a href=#"><img src="test.gif" /></a>',
                false
            ],
            'link with image and empty alt' => [
                '<a href=#"><img src="test.gif" alt=""/></a>',
                false
            ],
            'link with image and alt' => [
                '<a href=#"><img src="test.gif" alt="Alternative"/></a>',
                true
            ],
            'link with multiple images and at least one alt' => [
                '<a href=#"><img src="test.gif" alt=""/><img src="test.gif" alt="Alternative"/></a>',
                true
            ],
        ];
    }

    /**
     * @test
     * @dataProvider linkHasImageWithAltTestsDataProvider
     * @param $html
     * @param $expected
     */
    public function linkHasImageWithAltTests($html, $expected)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('a')->item(0);
        $result = LinkUtility::linkHasImageWithAlt($element);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function linkTextNotBlacklistedTestsDataProvider()
    {
        return [
            'text not blacklisted' => [
                '<a href="link.html">This is a link</a>',
                ['more', 'details'],
                true
            ],
            'text blacklisted exact case' => [
                '<a href="link.html">more</a>',
                ['more', 'details'],
                false
            ],
            'text blacklisted different case' => [
                '<a href="link.html">Details</a>',
                ['more', 'details'],
                false
            ],
            'only chars taken into account' => [
                '<a href="link.html">Details...</a>',
                ['more', 'details'],
                false
            ],
            'empty blacklist' => [
                '<a href="link.html">Details...</a>',
                [],
                true
            ],
            'empty test' => [
                '<a href="link.html"><img src="test.gif" alt="" /></a>',
                ['more', 'details'],
                true
            ],
        ];
    }

    /**
     * @dataProvider linkTextNotBlacklistedTestsDataProvider
     * @test
     * @param $html
     * @param $blacklist
     * @param $expected
     */
    public function linkTextNotBlacklistedTests($html, $blacklist, $expected)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('a')->item(0);
        $result = LinkUtility::linkTextNotBlacklisted($element, $blacklist);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function linkImageAttributeNotBlacklistedTestsDataProvider()
    {
        return [
            'no blacklist' => [
                '<a href=#" title="more">This is a link</a>',
                'title',
                [],
                true
            ],
            'no image' => [
                '<a href=#" title="more">This is a link</a>',
                'title',
                ['more', 'details'],
                true
            ],
            'image with no attribute' => [
                '<a href=#" title="more"><img src="test.gif" /></a>',
                'title',
                ['more', 'details'],
                true
            ],
            'image with attribute value not blacklisted' => [
                '<a href=#" title="more"><img src="test.gif" title="Not blacklisted" /></a>',
                'title',
                ['more', 'details'],
                true
            ],
            'image with attribute value blacklisted' => [
                '<a href=#" title="more"><img src="test.gif" title="Details" /></a>',
                'title',
                ['more', 'details'],
                false
            ],
            'image with attribute value blacklisted including extra chars' => [
                '<a href=#" title="more"><img src="test.gif" title="Details..." /></a>',
                'title',
                ['more', 'details'],
                false
            ],
        ];
    }

    /**
     * @test
     * @dataProvider linkImageAttributeNotBlacklistedTestsDataProvider
     * @param $html
     * @param $attribute
     * @param $blacklist
     * @param $expected
     */
    public function linkImageAttributeNotBlacklistedTests($html, $attribute, $blacklist, $expected)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('a')->item(0);
        $result = LinkUtility::linkImageAttributeNotBlacklisted($element, $attribute, $blacklist);

        $this->assertEquals($expected, $result);
    }
}
