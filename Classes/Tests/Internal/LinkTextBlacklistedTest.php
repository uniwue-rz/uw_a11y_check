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
class LinkTextBlacklistedTest extends AbstractTest
{
    protected string $id = 'link-name-blacklisted';
    protected string $helpUrl = '';
    protected int $impact = Result\Impact::MODERATE;
    protected array $blacklist = [];

    /**
     * LinkTextBlacklistedTest constructor.
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        parent::__construct($configuration);

        $blacklist = $configuration['blacklist'] ?? [];
        $this->blacklist = $this->initBlacklist($blacklist);

        $this->help .= '"' . implode('", "', $this->blacklist) . '"';
    }

    /**
     * Initializes the blacklist end ensures text is lowercase
     */
    protected function initBlacklist(array $blacklist): array
    {
        return array_map('mb_strtolower', $blacklist);
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
            $checkResult = (LinkUtility::linkTextNotBlacklisted($element, $this->blacklist) &&
                SharedUtility::elementAttributeValueNotBlacklisted($element, 'title', $this->blacklist) &&
                SharedUtility::elementAttributeValueNotBlacklisted($element, 'aria-label', $this->blacklist) &&
                LinkUtility::linkImageAttributeNotBlacklisted($element, 'alt', $this->blacklist) &&
                LinkUtility::linkImageAttributeNotBlacklisted($element, 'title', $this->blacklist)) ||
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
