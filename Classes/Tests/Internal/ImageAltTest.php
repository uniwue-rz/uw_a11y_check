<?php
namespace UniWue\UwA11yCheck\Tests\Internal;

use Symfony\Component\DomCrawler\Crawler;
use UniWue\UwA11yCheck\Check\Result;
use UniWue\UwA11yCheck\Tests\AbstractTest;
use UniWue\UwA11yCheck\Utility\Tests\ElementUidUtility;
use UniWue\UwA11yCheck\Utility\Tests\SharedUtility;

/**
 * Class ImageTest
 */
class ImageAltTest extends AbstractTest
{
    /**
     * @var string
     */
    protected $id = 'image-alt';

    /**
     * @var string
     */
    protected $helpUrl = 'https://dequeuniversity.com/rules/axe/3.4/image-alt';

    /**
     * @var int
     */
    protected $impact = Result\Impact::CRITICAL;

    /**
     * Runs the test
     *
     * @param string $html
     * @param int $fallbackElementUid
     * @return Result
     */
    public function run(string $html, int $fallbackElementUid): Result
    {
        $result = $this->initResultWithMetaDataFromTest();

        $crawler = new Crawler($html);
        $images = $crawler->filter('img');

        /** @var \DOMElement $image */
        foreach ($images as $image) {
            $checkResult = SharedUtility::elementHasAlt($image) ||
                SharedUtility::elementHasAriaLabelValue($image) ||
                SharedUtility::elementAriaLabelledByValueExistsAndNotEmpty($image, $crawler) ||
                SharedUtility::elementHasRolePresentation($image) ||
                SharedUtility::elementHasRoleNone($image);

            if (!$checkResult) {
                $node = new Result\Node();
                $node->setHtml($image->ownerDocument->saveHTML($image));
                $node->setUid($this->getElementUid($image, $fallbackElementUid));
                $result->addNode($node);
                $result->setStatus(Result\Status::VIOLATIONS);
            }
        }

        // If all found nodes passed, set status to passes
        if ($images->count() > 0 && count($result->getNodes()) === 0) {
            $result->setStatus(Result\Status::PASSES);
        }

        return $result;
    }
}
