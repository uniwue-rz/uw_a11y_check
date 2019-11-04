<?php
namespace UniWue\UwA11yCheck\Analyzers;

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