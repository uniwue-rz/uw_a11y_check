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
    protected $preset = null;

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
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function executeCheck(int $id)
    {
        return $this->preset->executeTestSuite($id);
    }
}
