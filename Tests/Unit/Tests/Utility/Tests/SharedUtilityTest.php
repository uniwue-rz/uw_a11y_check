<?php

namespace UniWue\UwA11yCheck\Tests\Unit\Tests\Internal;

/*
 * This file is part of the Extension "uw_a11y_check" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use DOMDocument;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\DomCrawler\Crawler;
use TYPO3\TestingFramework\Core\BaseTestCase;
use UniWue\UwA11yCheck\Utility\Tests\SharedUtility;

/**
 * Test cases for SharedUtilityTest
 */
class SharedUtilityTest extends BaseTestCase
{
    public static function elementHasRolePresentationTestsDataProvider(): array
    {
        return [
            'role presentation present' => [
                'presentation',
                true,
            ],
            'role presentation not present' => [
                'button',
                false,
            ],
        ];
    }

    #[DataProvider('elementHasRolePresentationTestsDataProvider')]
    #[Test]
    public function elementHasRolePresentationTests(string $role, bool $expected): void
    {
        $html = '<div role="' . $role . '">Test</div>';

        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('div')->item(0);
        $result = SharedUtility::elementHasRolePresentation($element);

        self::assertEquals($expected, $result);
    }

    public static function elementHasRoleNoneTestsDataProvider(): array
    {
        return [
            'role none present' => [
                'none',
                true,
            ],
            'role none not present' => [
                'button',
                false,
            ],
        ];
    }

    #[DataProvider('elementHasRoleNoneTestsDataProvider')]
    #[Test]
    public function elementHasRoleNoneTests(string $role, bool $expected): void
    {
        $html = '<div role="' . $role . '">Test</div>';

        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('div')->item(0);
        $result = SharedUtility::elementHasRoleNone($element);

        self::assertEquals($expected, $result);
    }

    public static function elementHasAriaLabelValueDataProvider(): array
    {
        return [
            'no aria label' => [
                '<div>Test</div>',
                false,
            ],
            'empty aria label' => [
                '<div aria-label="">Test</div>',
                false,
            ],
            'aria label with value' => [
                '<div aria-label="Label">Test</div>',
                true,
            ],
        ];
    }

    #[DataProvider('elementHasAriaLabelValueDataProvider')]
    #[Test]
    public function elementHasAriaLabelTests(string $html, bool $expected): void
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('div')->item(0);
        $result = SharedUtility::elementHasAriaLabelValue($element);

        self::assertEquals($expected, $result);
    }

    public static function elementAriaLabelledByValueExistsAndNotEmptyTestsDataProvider(): array
    {
        return [
            'labelledby element does not exist' => [
                '<div><a id="labelledByElement" href="#" aria-labelledby="notavailable">Link</a></div>',
                false,
            ],
            'aria-labelledby not set' => [
                '<div><a id="labelledByElement" href="#" >Link</a></div>',
                false,
            ],
            'labelledby element does exist but empty' => [
                '<div><label id="test"></label><a id="labelledByElement" href="#" aria-labelledby="test">Link</a></div>',
                false,
            ],
            'labelledby element does exist and not empty' => [
                '<div><label id="test">Label</label><a id="labelledByElement" href="#" aria-labelledby="test">Link</a></div>',
                true,
            ],
        ];
    }

    #[DataProvider('elementAriaLabelledByValueExistsAndNotEmptyTestsDataProvider')]
    #[Test]
    public function elementAriaLabelledByValueExistsAndNotEmptyTests(string $html, bool $expected): void
    {
        $crawler = new Crawler($html);

        /** @var \DOMElement $element */
        $element = $crawler->filter('#labelledByElement')->first()->getNode(0);
        $result = SharedUtility::elementAriaLabelledByValueExistsAndNotEmpty($element, $crawler);

        self::assertEquals($expected, $result);
    }

    public static function elementHasVisibleTextTestsDataProvider(): array
    {
        return [
            'no text in div' => [
                '<div></div>',
                false,
            ],
            'space in div' => [
                '<div> </div>',
                false,
            ],
            'image in div' => [
                '<div><img src="test.gif" alt="Alternative"></div>',
                false,
            ],
            'image in div with additional text in span' => [
                '<div><img src="test.gif" alt="Alternative"><span class="hidden">Text</span></div>',
                true,
            ],
            'text in div' => [
                '<div>Text</div>',
                true,
            ],
        ];
    }

    #[DataProvider('elementHasVisibleTextTestsDataProvider')]
    #[Test]
    public function elementHasVisibleTextTests(string $html, bool $expected): void
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('div')->item(0);
        $result = SharedUtility::elementHasVisibleText($element);

        self::assertEquals($expected, $result);
    }

    public static function elementHasNonEmptyTitleTestsDataProvider(): array
    {
        return [
            'no title' => [
                '<div></div>',
                false,
            ],
            'with title but empty' => [
                '<div title="">Test</div>',
                false,
            ],
            'with title and value' => [
                '<div title="Title">Test</div>',
                true,
            ],
            'no title but title in sub node' => [
                '<div><a href="#" title="title">Test</a></div>',
                false,
            ],
        ];
    }

    #[DataProvider('elementHasNonEmptyTitleTestsDataProvider')]
    #[Test]
    public function elementHasNonEmptyTitleTests(string $html, bool $expected): void
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('div')->item(0);
        $result = SharedUtility::elementHasNonEmptyTitle($element);

        self::assertEquals($expected, $result);
    }

    public static function elementHasAltTestsDataProvider(): array
    {
        return [
            'image with no alt' => [
                '<img src="test.gif">',
                false,
            ],
            'image with empty alt' => [
                '<img src="test.gif" alt="">',
                true,
            ],
            'image with alt' => [
                '<img src="test.gif" alt="alternative">',
                true,
            ],
        ];
    }

    #[DataProvider('elementHasAltTestsDataProvider')]
    #[Test]
    public function elementHasAltTests(string $html, bool $expected): void
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('img')->item(0);
        $result = SharedUtility::elementHasAlt($element);

        self::assertEquals($expected, $result);
    }

    public static function elementTitleNotRedundantTestsDataProvider(): array
    {
        return [
            'link has no title' => [
                '<a href="text.html">Test</a>',
                'a',
                true,
            ],
            'image has no title' => [
                '<img src="text.html" alt="" />',
                'img',
                true,
            ],
            'link has no redundant title' => [
                '<a href="text.html" title="Title">Link Text</a>',
                'a',
                true,
            ],
            'image has no redundant title' => [
                '<img src="text.html" title="Title" alt="Alternative" />',
                'img',
                true,
            ],
            'link has redundant title same case' => [
                '<a href="text.html" title="Title">Title</a>',
                'a',
                false,
            ],
            'image has redundant title same case' => [
                '<img src="text.html" title="Title" alt="Title" />',
                'img',
                false,
            ],
            'link has redundant title different case' => [
                '<a href="text.html" title="title">Title</a>',
                'a',
                false,
            ],
            'image has redundant title different case' => [
                '<img src="text.html" title="title" alt="Title" />',
                'img',
                false,
            ],
        ];
    }

    #[DataProvider('elementTitleNotRedundantTestsDataProvider')]
    #[Test]
    public function elementTitleNotRedundantTests(string $html, string $elementTag, bool $expected): void
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName($elementTag)->item(0);
        $result = SharedUtility::elementTitleNotRedundant($element);

        self::assertEquals($expected, $result);
    }

    public static function elementAttributeValueNotBlacklistedTestsDataProvider(): array
    {
        return [
            'attibute not found' => [
                '<a href="link.html">This is a link</a>',
                'title',
                ['more', 'details'],
                true,
            ],
            'attribute blacklisted exact case' => [
                '<a href="link.html" title="more">This is a link</a>',
                'title',
                ['more', 'details'],
                false,
            ],
            'attribute blacklisted different case' => [
                '<a href="link.html" title="More">This is a link</a>',
                'title',
                ['more', 'details'],
                false,
            ],
        ];
    }

    #[DataProvider('elementAttributeValueNotBlacklistedTestsDataProvider')]
    #[Test]
    public function elementAttributeValueNotBlacklistedTests(
        string $html,
        string $attribute,
        array $blacklist,
        bool $expected
    ): void {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName('a')->item(0);
        $result = SharedUtility::elementAttributeValueAllowed($element, $attribute, $blacklist);

        self::assertEquals($expected, $result);
    }

    public static function elementIsLinkWithHrefTestsDataProvider(): array
    {
        return [
            'no link' => [
                '<p>test</p>',
                'p',
                false,
            ],
            'link without link' => [
                '<a id="123">test</a>',
                'a',
                false,
            ],
            'link with empty href' => [
                '<a href="">test</a>',
                'a',
                true,
            ],
            'link with href' => [
                '<a href="test.html">test</a>',
                'a',
                true,
            ],
        ];
    }

    #[DataProvider('elementIsLinkWithHrefTestsDataProvider')]
    #[Test]
    public function elementIsLinkWithHrefTests(string $html, string $tag, bool $expected): void
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        /** @var \DOMElement $element */
        $element = $doc->getElementsByTagName($tag)->item(0);
        $result = SharedUtility::elementIsLinkWithHref($element);

        self::assertEquals($expected, $result);
    }
}
