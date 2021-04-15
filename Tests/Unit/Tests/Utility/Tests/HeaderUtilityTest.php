<?php

namespace UniWue\UwA11yCheck\Tests\Unit\Tests\Internal;

/*
 * This file is part of the Extension "uw_a11y_check" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use DOMDocument;
use DOMXPath;
use TYPO3\TestingFramework\Core\BaseTestCase;
use UniWue\UwA11yCheck\Utility\Tests\HeaderUtility;

/**
 * Test cases for HeaderUtilityTest
 */
class HeaderUtilityTest extends BaseTestCase
{
    /**
     * @return array
     */
    public function headersSequentiallyDescendingTestsDataProvider()
    {
        return [
            'h1 followed by h2' => [
                '<h1>h1</h1>',
                '<h2>h2</h2>',
                true
            ],
            'h1 followed by h3' => [
                '<h1>h1</h1>',
                '<h3>h3</h3>',
                false
            ],
            'h1 followed by h6' => [
                '<h1>h1</h1>',
                '<h6>h6</h6>',
                false
            ],
            'h3 followed by h2' => [
                '<h3>h3</h3>',
                '<h2>h2</h2>',
                true
            ],
        ];
    }

    /**
     * @test
     * @dataProvider headersSequentiallyDescendingTestsDataProvider
     * @param $htmlHeader1
     * @param $htmlHeader2
     * @param $expected
     */
    public function headersSequentiallyDescendingTests($htmlHeader1, $htmlHeader2, $expected)
    {
        $previousDocument = new DOMDocument();
        $previousDocument->loadHTML($htmlHeader1);
        $previousDocumentXPath = new DOMXPath($previousDocument);
        $previosHeader = $previousDocumentXPath->query('//h1|//h2|//h3|//h4|//h5|//h6')->item(0);

        $currentDocument = new DOMDocument();
        $currentDocument->loadHTML($htmlHeader2);
        $currentDocumentXPath = new DOMXPath($currentDocument);
        $currentHeader = $currentDocumentXPath->query('//h1|//h2|//h3|//h4|//h5|//h6')->item(0);

        $result = HeaderUtility::headersSequentiallyDescending($previosHeader, $currentHeader);

        self::assertEquals($expected, $result);
    }
}
