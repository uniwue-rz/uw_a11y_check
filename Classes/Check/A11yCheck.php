<?php

namespace UniWue\UwA11yCheck\Check;

/**
 * Class A11yCheck
 */
class A11yCheck
{
    /**
     * @var Preset
     */
    protected $preset;

    /**
     * A11yCheck constructor.
     *
     * @param Preset $preset
     */
    public function __construct(Preset $preset)
    {
        $this->preset = $preset;
    }

    /**
     * Executes the check and returns the result as objectStorage
     *
     * @param int $id
     * @param int $levels
     * @return array
     */
    public function executeCheck(int $id, int $levels = 0): array
    {
        return $this->preset->executeTestSuiteByPageUid($id, $levels);
    }
}
