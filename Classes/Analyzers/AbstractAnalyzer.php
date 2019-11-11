<?php
namespace UniWue\UwA11yCheck\Analyzers;

use GuzzleHttp\Client;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use UniWue\UwA11yCheck\Check\Preset;
use UniWue\UwA11yCheck\Check\ResultSet;
use UniWue\UwA11yCheck\Tests\TestInterface;

/**
 * Class AbstractAnalyzer
 */
abstract class AbstractAnalyzer implements AnalyzerInterface
{
    const TYPE_INTERNAL = 'internal';

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var array
     */
    protected $pageUids = [];

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Runs all tests for the given preset and recordUid
     *
     * @param Preset $preset
     * @param int $recordUid
     * @return ResultSet
     */
    public function runTests(Preset $preset, int $recordUid): ResultSet
    {
        $results = new ObjectStorage();
        $testSuite = $preset->getTestSuite();

        $url = $preset->getCheckUrl($recordUid);
        $html = $this->fetchHtml($url);

        /** @var TestInterface $test */
        foreach ($testSuite->getTests() as $test) {
            $result = $test->run($html, $recordUid);
            $results->attach($result);
        }

        $resultSet = GeneralUtility::makeInstance(ResultSet::class);
        $resultSet->setUid($recordUid);
        $resultSet->setResults($results);
        $resultSet->setTable($preset->getCheckTableName());

        return $resultSet;
    }

    /**
     * Initializes the pageUids to check
     *
     * @param int $pageUid
     * @param int $levels
     * @return void
     */
    public function initializePageUids(int $pageUid, int $levels = 0): void
    {
        $pageUids = [$pageUid];
        if ($levels > 0) {
            $queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);
            $pidList = $queryGenerator->getTreeList($pageUid, $levels, 0, 1);
            $pageUids = GeneralUtility::intExplode(',', $pidList, true);
        }

        $this->pageUids = $pageUids;
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
