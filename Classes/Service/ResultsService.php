<?php

namespace UniWue\UwA11yCheck\Service;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use UniWue\UwA11yCheck\Check\ResultSet;

/**
 * Class ResultsService
 */
class ResultsService
{
    protected SerializationService $serializationService;
    protected PresetService $presetService;

    public function injectSerializationService(SerializationService $serializationService): void
    {
        $this->serializationService = $serializationService;
    }

    public function injectPresetService(PresetService $presetService): void
    {
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
                    $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)
                )
            )->orderBy('preset_id', 'asc');

        $queryResult = $query->execute()->fetchAllAssociative();

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
                    'preset' => $this->presetService->getPresetById($presetId) ?? 'Unknown',
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
                    $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)
                )
            )->orderBy('preset_id', 'asc');

        return $query->execute()->fetchOne();
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
                    $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)
                )
            );

        $query->execute();
    }
}
