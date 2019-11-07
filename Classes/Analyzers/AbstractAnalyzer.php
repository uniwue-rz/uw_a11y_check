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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
