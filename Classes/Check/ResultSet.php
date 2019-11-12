<?php
namespace UniWue\UwA11yCheck\Check;

use UniWue\UwA11yCheck\Check\Result\Impact;

/**
 * Class ResultSet
 */
class ResultSet
{
    /**
     * @var int
     */
    protected $pid = 0;

    /**
     * @var int
     */
    protected $uid = 0;

    /**
     * @var string
     */
    protected $table = '';

    /**
     * @var bool
     */
    protected $failed = false;

    /**
     * @var string
     */
    protected $failedMessage = '';

    /**
     * @var array
     */
    protected $results = [];

    /**
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * @param int $pid
     */
    public function setPid(int $pid): void
    {
        $this->pid = $pid;
    }

    /**
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @param array $results
     */
    public function setResults(array $results): void
    {
        $this->results = $results;
    }

    /**
     * @return int
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    /**
     * @param int $uid
     */
    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * @return bool
     */
    public function getFailed(): bool
    {
        return $this->failed;
    }

    /**
     * @param bool $failed
     */
    public function setFailed(bool $failed): void
    {
        $this->failed = $failed;
    }

    /**
     * @return string
     */
    public function getFailedMessage(): string
    {
        return $this->failedMessage;
    }

    /**
     * @param string $failedMessage
     */
    public function setFailedMessage(string $failedMessage): void
    {
        $this->failedMessage = $failedMessage;
    }

    /**
     * Returns the highest impact of all results
     *
     * @return int
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
}
