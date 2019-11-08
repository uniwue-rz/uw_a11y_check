<?php
namespace UniWue\UwA11yCheck\Tests\Internal;

use Symfony\Component\DomCrawler\Crawler;
use UniWue\UwA11yCheck\Check\Result;
use UniWue\UwA11yCheck\Tests\AbstractTest;
use UniWue\UwA11yCheck\Utility\Tests\ElementUidUtility;
use UniWue\UwA11yCheck\Utility\Tests\SharedUtility;

/**
 * Class RedundantTitleTest
 */
class RedundantTitleTest extends AbstractTest
{
    /**
     * @var string
     */
    protected $id = 'redundant-title';

    /**
     * @var string
     */
    protected $description = 'Ensures title attribute text is not the same as text or alternative text.';

    /**
     * @var string
     */
    protected $help = 'The title attribute value is used to provide advisory information. It typically appears when the users hovers the mouse over an element. The advisory information presented should not be identical to or very similar to the element text or alternative text.';

    /**
     * @var string
     */
    protected $helpUrl = '';

    /**
     * @var int
     */
    protected $impact = Result\Impact::MINOR;

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
        $elements = $crawler->filter('a, img');

        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $checkResult = SharedUtility::elementTitleNotRedundant($element);

            if (!$checkResult) {
                $node = new Result\Node();
                $node->setHtml($element->ownerDocument->saveHTML($element));
                $node->setUid(ElementUidUtility::determineElementUid($element));
                $result->addNode($node);
                $result->setStatus(Result\Status::VIOLATIONS);
            }
        }

        // If all found nodes passed, set status to passes
        if ($elements->count() > 0 && $result->getNodes()->count() === 0) {
            $result->setStatus(Result\Status::PASSES);
        }

        return $result;
    }
}
