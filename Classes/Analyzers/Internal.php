<?php
namespace UniWue\UwA11yCheck\Analyzers;

use GuzzleHttp\Client;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use UniWue\UwA11yCheck\Check\TestSuite;
use UniWue\UwA11yCheck\Tests\TestInterface;

/**
 * Class Internal
 */
class Internal extends AbstractAnalyzer
{
    /**
     * @var string
     */
    protected $type = AbstractAnalyzer::TYPE_INTERNAL;

    /**
     * Executes all tests in the testSuite
     *A
     * @param string $url
     * @param TestSuite $testSuite
     */
    public function executeTestSuite(string $url, TestSuite $testSuite): void
    {
        $html = $this->fetchHtml($url);

        /** @var TestInterface $test */
        foreach ($testSuite->getTests() as $test) {
            $result = $test->run($html);
            $this->addResult($result);
        }
    }

    /**
     * Fetches the resulting HTML from the given URL
     *
     * @param string $url
     * @return string
     */
    protected function fetchHtml(string $url): string
    {
        $client = GeneralUtility::makeInstance(Client::class);
        $response = $client->get($url);

        // @todo: Check Response for errors

        return $response->getBody()->getContents();
    }
}
