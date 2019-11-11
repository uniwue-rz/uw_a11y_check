<?php
declare(strict_types = 1);
namespace UniWue\UwA11yCheck\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use UniWue\UwA11yCheck\Check\Preset;
use UniWue\UwA11yCheck\Check\ResultSet;

/**
 * Class AbstractCheckCommand
 */
abstract class AbstractCheckCommand extends Command
{
    /**
     * @var Serializer
     */
    protected $serializer = null;

    /**
     * AbstractCheckCommand constructor.
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * Saves the given results
     *
     * @param Preset $preset
     * @param array $results
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
     *
     * @param Preset $preset
     * @param ResultSet $resultSet
     *
     * @return void
     */
    protected function saveResult(Preset $preset, ResultSet $resultSet): void
    {
        $serializedData = $this->serializer->serialize($resultSet, 'json');

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
                    $queryBuilder->createNamedParameter($preset->getId(), \PDO::PARAM_STR)
                )
            )
            ->execute();
    }
}
