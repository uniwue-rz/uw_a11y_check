<?php
namespace UniWue\UwA11yCheck\Analyzers;

use GuzzleHttp\Client;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
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
     * @param TestSuite $testSuite
     * @param string $url
     * @return ObjectStorage
     */
    public function executeTestSuite(TestSuite $testSuite, string $url): ObjectStorage
    {
        $results = new ObjectStorage();
        $html = $this->fetchHtml($url);

        /** @var TestInterface $test */
        foreach ($testSuite->getTests() as $test) {
            $result = $test->run($html);
            $results->attach($result);
        }

        return $results;
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
