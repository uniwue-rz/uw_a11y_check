<?php

namespace UniWue\UwA11yCheck\Domain\Model\Dto;

/**
 * Class SingleTableDemand
 */
class SingleTableDemand
{
    protected int $pid;

    /**
     * The tablename for the query
     */
    protected string $tableName = '';

    /**
     * Max results of records to return. Default is 50
     */
    protected int $maxResults = 50;

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function setTableName(string $tableName): void
    {
        $this->tableName = $tableName;
    }

    public function getMaxResults(): int
    {
        return $this->maxResults;
    }

    public function setMaxResults(int $maxResults): void
    {
        $this->maxResults = $maxResults;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function setPid(int $pid): void
    {
        $this->pid = $pid;
    }
}
