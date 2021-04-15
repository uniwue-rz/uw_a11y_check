<?php

namespace UniWue\UwA11yCheck\Domain\Model\Dto;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class SingleTableDemand
 */
class SingleTableDemand extends AbstractEntity
{
    /**
     * The tablename for the query
     *
     * @var string
     */
    protected $tableName = '';

    /**
     * Max results of records to return. Default is 50
     *
     * @var int
     */
    protected $maxResults = 50;

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     */
    public function setTableName(string $tableName): void
    {
        $this->tableName = $tableName;
    }

    /**
     * @return int
     */
    public function getMaxResults(): int
    {
        return $this->maxResults;
    }

    /**
     * @param int $maxResults
     */
    public function setMaxResults(int $maxResults): void
    {
        $this->maxResults = $maxResults;
    }
}
