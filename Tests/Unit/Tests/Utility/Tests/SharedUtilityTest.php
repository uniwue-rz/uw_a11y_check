<?php
namespace UniWue\UwA11yCheck\Tests\Unit\Tests\Internal;

/*
 * This file is part of the Extension "uw_a11y_check" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use DOMDocument;
use Symfony\Component\DomCrawler\Crawler;
use TYPO3\TestingFramework\Core\BaseTestCase;
use UniWue\UwA11yCheck\Utility\Tests\SharedUtility;

/**
 * Test cases for SharedUtilityTest
 */
class SharedUtilityTest extends BaseTestCase
{
    /**
     * @return array
     */
    public function elementHasRolePresentationTestsDataProvider()
    {
        return [
            'role presentation present' => [
                'presentation',
                true
            ],
            'role presentation not present' => [
                'button',
                false
            ]
        ];
    }

    /**
     * @test
     * @dataProvider elementHasRolePresentationTestsDataProvider
     * @param $role
     * @param $expected
     */
    public function elementHasRolePresentationTests($role, $expected)
    {
        $html = '<div role="' . $role . '">Test</div>';

        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('div')->item(0);
        $result = SharedUtility::elementHasRolePresentation($element);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function elementHasRoleNoneTestsDataProvider()
    {
        return [
            'role none present' => [
                'none',
                true
            ],
            'role none not present' => [
                'button',
                false
            ]
        ];
    }

    /**
     * @test
     * @dataProvider elementHasRoleNoneTestsDataProvider
     * @param $role
     * @param $expected
     */
    public function elementHasRoleNoneTests($role, $expected)
    {
        $html = '<div role="' . $role . '">Test</div>';

        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('div')->item(0);
        $result = SharedUtility::elementHasRoleNone($element);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function elementHasAriaLabelValueDataProvider()
    {
        return [
            'no aria label' => [
                '<div>Test</div>',
                false
            ],
            'empty aria label' => [
                '<div aria-label="">Test</div>',
                false
            ],
            'aria label with value' => [
                '<div aria-label="Label">Test</div>',
                true
            ]
        ];
    }

    /**
     * @test
     * @dataProvider elementHasAriaLabelValueDataProvider
     * @param $html
     * @param $expected
     */
    public function elementHasAriaLabelTests($html, $expected)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('div')->item(0);
        $result = SharedUtility::elementHasAriaLabelValue($element);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function elementAriaLabelledByValueExistsAndNotEmptyTestsDataProvider()
    {
        return [
            'labelledby element does not exist' => [
                '<div><a id="labelledByElement" href="#" aria-labelledby="notavailable">Link</a></div>',
                false
            ],
            'aria-labelledby not set' => [
                '<div><a id="labelledByElement" href="#" >Link</a></div>',
                false
            ],
            'labelledby element does exist but empty' => [
                '<div><label id="test"></label><a id="labelledByElement" href="#" aria-labelledby="test">Link</a></div>',
                false
            ],
            'labelledby element does exist and not empty' => [
                '<div><label id="test">Label</label><a id="labelledByElement" href="#" aria-labelledby="test">Link</a></div>',
                true
            ],
        ];
    }

    /**
     * @test
     * @dataProvider elementAriaLabelledByValueExistsAndNotEmptyTestsDataProvider
     * @param $html
     * @param $expected
     */
    public function elementAriaLabelledByValueExistsAndNotEmptyTests($html, $expected)
    {
        $crawler = new Crawler($html);

        /** @var \DOMElement $element */
        $element = $crawler->filter('#labelledByElement')->first()->getNode(0);
        $result = SharedUtility::elementAriaLabelledByValueExistsAndNotEmpty($element, $crawler);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function elementHasVisibleTextTestsDataProvider()
    {
        return [
            'no text in div' => [
                '<div></div>',
                false
            ],
            'image in div' => [
                '<div><img src="test.gif" alt="Alternative"></div>',
                false
            ],
            'image in div with additional text in span' => [
                '<div><img src="test.gif" alt="Alternative"><span class="hidden">Text</span></div>',
                true
            ],
            'text in div' => [
                '<div>Text</div>',
                true
            ],
        ];
    }

    /**
     * @test
     * @dataProvider elementHasVisibleTextTestsDataProvider
     * @param $html
     * @param $expected
     */
    public function elementHasVisibleTextTests($html, $expected)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('div')->item(0);
        $result = SharedUtility::elementHasVisibleText($element);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function elementHasNonEmptyTitleTestsDataProvider()
    {
        return [
            'no title' => [
                '<div></div>',
                false
            ],
            'with title but empty' => [
                '<div title="">Test</div>',
                false
            ],
            'with title and value' => [
                '<div title="Title">Test</div>',
                true
            ],
            'no title but title in sub node' => [
                '<div><a href="#" title="title">Test</a></div>',
                false
            ],
        ];
    }

    /**
     * @test
     * @dataProvider elementHasNonEmptyTitleTestsDataProvider
     * @param $html
     * @param $expected
     */
    public function elementHasNonEmptyTitleTests($html, $expected)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('div')->item(0);
        $result = SharedUtility::elementHasNonEmptyTitle($element);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function elementHasAltTestsDataProvider()
    {
        return [
            'image with no alt' => [
                '<img src="test.gif">',
                false
            ],
            'image with empty alt' => [
                '<img src="test.gif" alt="">',
                true
            ],
            'image with alt' => [
                '<img src="test.gif" alt="alternative">',
                true
            ],
        ];
    }

    /**
     * @test
     * @dataProvider elementHasAltTestsDataProvider
     * @param $html
     * @param $expected
     */
    public function elementHasAltTests($html, $expected)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('img')->item(0);
        $result = SharedUtility::elementHasAlt($element);

        $this->assertEquals($expected, $result);
    }
}
