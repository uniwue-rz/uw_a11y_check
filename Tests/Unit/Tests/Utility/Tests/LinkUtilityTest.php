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
}
