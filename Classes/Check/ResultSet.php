<?php

namespace UniWue\UwA11yCheck\Check;

use UniWue\UwA11yCheck\Check\Result\Impact;

/**
 * Class ResultSet
 */
class ResultSet
{
    protected int $pid = 0;
    protected int $uid = 0;
    protected string $table = '';
    protected string $editRecordTable = '';
    protected bool $failed = false;
    protected string $failedMessage = '';
    protected string $checkedUrl = '';
    protected array $results = [];

    public function getPid(): int
    {
        return $this->pid;
    }

    public function setPid(int $pid): void
    {
        $this->pid = $pid;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function setResults(array $results): void
    {
        $this->results = $results;
    }

    public function getUid(): int
    {
        return $this->uid;
    }

    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    public function getFailed(): bool
    {
        return $this->failed;
    }

    public function setFailed(bool $failed): void
    {
        $this->failed = $failed;
    }

    public function getFailedMessage(): string
    {
        return $this->failedMessage;
    }

    public function setFailedMessage(string $failedMessage): void
    {
        $this->failedMessage = $failedMessage;
    }

    public function getEditRecordTable(): string
    {
        return $this->editRecordTable;
    }

    public function setEditRecordTable(string $editRecordTable): void
    {
        $this->editRecordTable = $editRecordTable;
    }

    /**
     * Returns the highest impact of all results
     */
    public function getImpact(): int
    {
        $impact = Impact::NONE;

        if ($this->getFailed()) {
            return Impact::FAILED;
        }

        /** @var Result $result */
        foreach ($this->results as $result) {
            if (count($result->getNodes()) > 0 && $result->getImpact() > $impact) {
                $impact = $result->getImpact();
            }
        }
        return $impact;
    }

    public function getCheckedUrl(): string
    {
        return $this->checkedUrl;
    }

    public function setCheckedUrl(string $checkedUrl): void
    {
        $this->checkedUrl = $checkedUrl;
    }
}
