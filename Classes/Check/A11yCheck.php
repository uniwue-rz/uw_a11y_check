<?php

namespace UniWue\UwA11yCheck\Check;

/**
 * Class A11yCheck
 */
class A11yCheck
{
    protected Preset $preset;

    /**
     * A11yCheck constructor.
     */
    public function __construct(Preset $preset)
    {
        $this->preset = $preset;
    }

    /**
     * Executes the check and returns the result as objectStorage
     */
    public function executeCheck(int $id, int $levels = 0): array
    {
        return $this->preset->executeTestSuiteByPageUid($id, $levels);
    }
}
