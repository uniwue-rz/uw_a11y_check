<?php

namespace UniWue\UwA11yCheck\Analyzers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use UniWue\UwA11yCheck\Check\Preset;
use UniWue\UwA11yCheck\Check\ResultSet;
use UniWue\UwA11yCheck\Tests\TestInterface;
use UniWue\UwA11yCheck\Utility\QueryGenerator;

/**
 * Class AbstractAnalyzer
 */
abstract class AbstractAnalyzer implements AnalyzerInterface
{
    public const TYPE_INTERNAL = 'internal';

    protected string $type = '';
    protected array $pageUids = [];

    public function getType(): string
    {
        return $this->type;
    }
    /**
     * Runs all tests for the given preset and recordUid
     */
    public function runTests(Preset $preset, int $recordUid): ResultSet
    {
        $results = [];
        $testSuite = $preset->getTestSuite();

        $pid = $this->getPidByRecordUid($preset->getCheckTableName(), $recordUid);
        $resultSet = GeneralUtility::makeInstance(ResultSet::class);
        $url = $preset->getCheckUrl($recordUid);

        try {
            $html = $this->fetchHtml($url);

            /** @var TestInterface $test */
            foreach ($testSuite->getTests() as $test) {
                $result = $test->run($html, $recordUid);
                $results[] = $result;
            }
        } catch (ClientException $e) {
            $resultSet->setFailed(true);
            $resultSet->setFailedMessage($e->getMessage());
        }

        $resultSet->setUid($recordUid);
        $resultSet->setPid($pid);
        $resultSet->setResults($results);
        $resultSet->setTable($preset->getCheckTableName());
        $resultSet->setEditRecordTable($preset->getEditRecordTableName());
        $resultSet->setCheckedUrl($url);

        return $resultSet;
    }

    /**
     * Initializes the pageUids to check
     */
    public function initializePageUids(int $pageUid, int $levels = 0): void
    {
        $pageUids = [$pageUid];
        if ($levels > 0) {
            $queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);
            $pidList = $queryGenerator->getTreeList($pageUid, $levels, 0);
            $pageUids = GeneralUtility::intExplode(',', $pidList, true);
        }

        $this->pageUids = $pageUids;
    }

    /**
     * Returns the PID of the record
     */
    protected function getPidByRecordUid(string $table, int $recordUid): int
    {
        if ($table === 'pages') {
            return $recordUid;
        }

        $record = BackendUtility::getRecord($table, $recordUid, 'pid');
        return $record['pid'] ?? 0;
    }

    /**
     * Fetches the resulting HTML from the given URL
     */
    protected function fetchHtml(string $url): string
    {
        $client = GeneralUtility::makeInstance(Client::class, ['verify' => false ]);
        $response = $client->get($url);

        // @todo: Move make verify=>false to configurable
        // @todo: Check Response for errors

        return $response->getBody()->getContents();
    }
}
