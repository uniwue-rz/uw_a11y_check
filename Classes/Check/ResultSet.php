<?php
namespace UniWue\UwA11yCheck\Check;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
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
     * @var ObjectStorage
     */
    protected $results = null;

    /**
     * ResultSet constructor.
     */
    public function __construct()
    {
        $this->results = new ObjectStorage();
    }

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
     * @return ObjectStorage
     */
    public function getResults(): ObjectStorage
    {
        return $this->results;
    }

    /**
     * @param ObjectStorage $results
     */
    public function setResults(ObjectStorage $results): void
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
     * Returns the highest impact of all results
     *
     * @return int
     */
    public function getImpact(): int
    {
        $impact = Impact::NONE;

        /** @var Result $result */
        foreach ($this->results as $result) {
            if ($result->getNodes()->count() > 0 && $result->getImpact() > $impact) {
                $impact = $result->getImpact();
            }
        }
        return $impact;
    }
}
