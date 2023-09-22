<?php

declare(strict_types=1);

namespace UniWue\UwA11yCheck\Command;

use Symfony\Component\Console\Command\Command;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use UniWue\UwA11yCheck\Check\Preset;
use UniWue\UwA11yCheck\Check\ResultSet;
use UniWue\UwA11yCheck\Service\SerializationService;

/**
 * Class AbstractCheckCommand
 */
abstract class AbstractCheckCommand extends Command
{
    protected SerializationService $serializationService;

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->serializationService = new SerializationService();
    }

    /**
     * Saves the given results
     */
    public function saveResults(Preset $preset, array $results): void
    {
        /** @var ResultSet $resultSet */
        foreach ($results as $resultSet) {
            $this->cleanupOldResults($preset, $resultSet);
            $this->saveResult($preset, $resultSet);
        }
    }

    /**
     * Saves a single result to the database
     */
    protected function saveResult(Preset $preset, ResultSet $resultSet): void
    {
        $serializedData = $this->serializationService->getSerializer()->serialize($resultSet, 'json');

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_a11ycheck_result');
        $queryBuilder
            ->insert('tx_a11ycheck_result')
            ->values([
                'pid' => $resultSet->getPid(),
                'check_date' => time(),
                'preset_id' => $preset->getId(),
                'record_uid' => $resultSet->getUid(),
                'table_name' => $preset->getCheckTableName(),
                'resultset' => $serializedData,
            ])
            ->execute();
    }

    /**
     * Cleans up old check results in the database
     *
     * @param Preset $preset
     * @param ResultSet $resultSet
     */
    protected function cleanupOldResults(Preset $preset, ResultSet $resultSet): void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_a11ycheck_result');
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder
            ->delete('tx_a11ycheck_result')
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($resultSet->getPid(), \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'record_uid',
                    $queryBuilder->createNamedParameter($resultSet->getUid(), \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'preset_id',
                    $queryBuilder->createNamedParameter($preset->getId())
                )
            )
            ->execute();
    }
}
