<?php
namespace UniWue\UwA11yCheck\Check;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use UniWue\UwA11yCheck\Check\Result\Impact;

/**
 * Class ResultSet
 */
class ResultSet
{
    protected $pid = 0;

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
