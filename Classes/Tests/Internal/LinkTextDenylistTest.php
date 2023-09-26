<?php

namespace UniWue\UwA11yCheck\Tests\Internal;

use Symfony\Component\DomCrawler\Crawler;
use UniWue\UwA11yCheck\Check\Result;
use UniWue\UwA11yCheck\Tests\AbstractTest;
use UniWue\UwA11yCheck\Utility\Tests\LinkUtility;
use UniWue\UwA11yCheck\Utility\Tests\SharedUtility;

/**
 * Class LinkNameBlacklistedTest
 */
class LinkTextDenylistTest extends AbstractTest
{
    protected string $id = 'link-name-denylist';
    protected string $helpUrl = '';
    protected int $impact = Result\Impact::MODERATE;
    protected array $denylist = [];

    /**
     * LinkTextBlacklistedTest constructor.
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        parent::__construct($configuration);

        $denylist = $configuration['denylist'] ?? [];
        $this->denylist = $this->initDenylist($denylist);

        $this->help .= '"' . implode('", "', $this->denylist) . '"';
    }

    /**
     * Initializes the denylist end ensures text is lowercase
     */
    protected function initDenylist(array $denylist): array
    {
        return array_map('mb_strtolower', $denylist);
    }

    /**
     * Runs the test
     */
    public function run(string $html, int $fallbackElementUid): Result
    {
        $result = $this->initResultWithMetaDataFromTest();

        $crawler = new Crawler($html);
        $elements = $crawler->filter('a');

        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $checkResult = (LinkUtility::linkTextAllowed($element, $this->denylist) &&
                SharedUtility::elementAttributeValueAllowed($element, 'title', $this->denylist) &&
                SharedUtility::elementAttributeValueAllowed($element, 'aria-label', $this->denylist) &&
                LinkUtility::linkImageAttributeAllowed($element, 'alt', $this->denylist) &&
                LinkUtility::linkImageAttributeAllowed($element, 'title', $this->denylist)) ||
                SharedUtility::elementHasRolePresentation($element) ||
                SharedUtility::elementHasRoleNone($element);

            if (!$checkResult) {
                $node = new Result\Node();
                $node->setHtml($element->ownerDocument->saveHTML($element));
                $node->setUid($this->getElementUid($element, $fallbackElementUid));
                $result->addNode($node);
                $result->setStatus(Result\Status::VIOLATIONS);
            }
        }

        // If all found nodes passed, set status to passes
        if ($elements->count() > 0 && count($result->getNodes()) === 0) {
            $result->setStatus(Result\Status::PASSES);
        }

        return $result;
    }
}
