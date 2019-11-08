<?php
namespace UniWue\UwA11yCheck\Tests\Internal;

use Symfony\Component\DomCrawler\Crawler;
use UniWue\UwA11yCheck\Check\Result;
use UniWue\UwA11yCheck\Tests\AbstractTest;
use UniWue\UwA11yCheck\Utility\Tests\ElementUidUtility;
use UniWue\UwA11yCheck\Utility\Tests\LinkUtility;
use UniWue\UwA11yCheck\Utility\Tests\SharedUtility;

/**
 * Class LinkTest
 */
class LinkNameTest extends AbstractTest
{
    /**
     * @var string
     */
    protected $id = 'link-name';

    /**
     * @var string
     */
    protected $helpUrl = 'https://dequeuniversity.com/rules/axe/3.4/link-name';

    /**
     * @var int
     */
    protected $impact = Result\Impact::SERIOUS;

    /**
     * Runs the test
     *
     * @param string $html
     * @return Result
     */
    public function run(string $html): Result
    {
        $result = $this->initResultWithMetaDataFromTest();

        $crawler = new Crawler($html);
        $links = $crawler->filter('a');

        /** @var \DOMElement $link */
        foreach ($links as $link) {
            $checkResult = SharedUtility::elementHasVisibleText($link) ||
                SharedUtility::elementHasAriaLabelValue($link) ||
                SharedUtility::elementAriaLabelledByValueExistsAndNotEmpty($link, $crawler) ||
                SharedUtility::elementHasRoleNone($link) ||
                SharedUtility::elementHasRolePresentation($link) ||
                LinkUtility::linkHasImageWithAlt($link);

            if (!$checkResult) {
                $node = new Result\Node();
                $node->setHtml($link->ownerDocument->saveHTML($link));
                $node->setUid(ElementUidUtility::determineElementUid($link));
                $result->addNode($node);
                $result->setStatus(Result\Status::VIOLATIONS);
            }
        }

        // If all found nodes passed, set status to passes
        if ($links->count() > 0 && $result->getNodes()->count() === 0) {
            $result->setStatus(Result\Status::PASSES);
        }

        return $result;
    }
}
