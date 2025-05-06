<?php

namespace UniWue\UwA11yCheck\Service;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use UniWue\UwA11yCheck\Check\ResultSet;

/**
 * Class ResultsService
 */
class ResultsService
{
    protected SerializationService $serializationService;
    protected PresetService $presetService;

    public function __construct(SerializationService $serializationService, PresetService $presetService)
    {
        $this->serializationService = $serializationService;
        $this->presetService = $presetService;
    }

    /**
     * Returns all saved results from the database. An array is returned containing both the presets and
     * the check results.
     */
    public function getResultsArrayByPid(int $pid): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_a11ycheck_result');
        $queryBuilder->getRestrictions()->removeAll();
        $query = $queryBuilder
            ->select('*')
            ->from('tx_a11ycheck_result')
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT)
                )
            )->orderBy('preset_id', 'asc');

        $queryResult = $query->executeQuery()->fetchAllAssociative();

        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($pid);
        $dbResults = [];

        foreach ($queryResult as $result) {
            $unserializedData = $this->serializationService->getSerializer()->deserialize(
                $result['resultset'],
                ResultSet::class,
                'json'
            );

            $presetId = $result['preset_id'];

            if (!isset($dbResults[$presetId])) {
                $checkDate = new \DateTime();
                $checkDate->setTimestamp($result['check_date']);
                $dbResults[$presetId] = [
                    'preset' => $this->presetService->getPresetById($presetId, $site) ?? 'Unknown',
                    'results' => [$unserializedData],
                    'date' => $checkDate,
                ];
            } else {
                $dbResults[$presetId]['results'][] = $unserializedData;
            }
        }

        return $dbResults;
    }

    /**
     * Returns the amount of saved DB check results
     */
    public function getSavedResultsCount(int $pid): int
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_a11ycheck_result');
        $queryBuilder->getRestrictions()->removeAll();
        $query = $queryBuilder
            ->count('uid')
            ->from('tx_a11ycheck_result')
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT)
                )
            )->orderBy('preset_id', 'asc');

        return $query->executeQuery()->fetchOne();
    }

    /**
     * Deleted the saved result for the given PID
     */
    public function deleteSavedResults(int $pid): void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_a11ycheck_result');
        $queryBuilder->getRestrictions()->removeAll();
        $query = $queryBuilder
            ->delete('tx_a11ycheck_result')
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT)
                )
            );

        $query->executeStatement();
    }
}
