<?php

namespace UniWue\UwA11yCheck\Analyzers;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use UniWue\UwA11yCheck\Check\Preset;
use UniWue\UwA11yCheck\Domain\Model\Dto\SingleTableDemand;

/**
 * Class NewsAnalyzer
 */
class NewsAnalyzer extends AbstractAnalyzer
{
    /**
     * @var string
     */
    protected $type = AbstractAnalyzer::TYPE_INTERNAL;

    /**
     * Return an aray of news record Uids to check
     *
     * @param Preset $preset
     * @return array
     */
    public function getCheckRecordUids(Preset $preset): array
    {
        $newsUids = [];

        foreach ($this->pageUids as $pageUid) {
            $demand = GeneralUtility::makeInstance(SingleTableDemand::class);
            $demand->setTableName($preset->getCheckTableName());
            $demand->setPid($pageUid);

            // @todo: Consider more demand settings (e.g. date limit - must be TYPO3 general fields in the first place)

            $newsUids = array_merge($newsUids, $this->getNewsRecordUids($demand));
        }

        return $newsUids;
    }

    /**
     * Returns all news UIDs matching the given demand
     *
     * @param SingleTableDemand $demand
     * @return array
     */
    protected function getNewsRecordUids(SingleTableDemand $demand)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($demand->getTableName());

        $query = $queryBuilder
            ->select('uid')
            ->from($demand->getTableName())
            ->orderBy('datetime', 'desc');

        $constraints = [];

        // We only select real news records (no redirects)
        $constraints[] = $queryBuilder->expr()->eq(
            'type',
            $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
        );

        // Only include news with sys_language_uid = 0
        $constraints[] = $queryBuilder->expr()->eq(
            'sys_language_uid',
            $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
        );

        if ($demand->getPid() > 0) {
            $constraints[] = $queryBuilder->expr()->eq(
                'pid',
                $queryBuilder->createNamedParameter($demand->getPid(), Connection::PARAM_INT)
            );
        }

        $query->where(...$constraints);

        if ($demand->getMaxResults() > 0) {
            $query->setMaxResults($demand->getMaxResults());
        }

        $queryResult = $query->execute()->fetchAll();

        $uidList = [];
        foreach ($queryResult as $record) {
            $uidList[] = $record['uid'];
        }

        return $uidList;
    }
}
