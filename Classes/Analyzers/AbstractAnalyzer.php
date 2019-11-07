<?php
namespace UniWue\UwA11yCheck\Analyzers;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use UniWue\UwA11yCheck\Check\Result;

/**
 * Class AbstractAnalyzer
 */
abstract class AbstractAnalyzer implements AnalyzerInterface
{
    const TYPE_INTERNAL = 'internal';

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var ObjectStorage
     */
    protected $results = null;

    /**
     * AbstractAnalyzer constructor.
     */
    public function __construct()
    {
        $this->results = new ObjectStorage();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
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

    public function addResult(Result $result)
    {
        $this->results->attach($result);
    }

    /**
     * @param Result $result
     */
    public function removeResult(Result $result)
    {
        $this->results->detach($result);
    }

    /**
     * @param array $results
     */
    public function addResultsFromArray(array $results)
    {
        foreach ($results as $result) {
            $this->results->attach($result);
        }
    }
}
