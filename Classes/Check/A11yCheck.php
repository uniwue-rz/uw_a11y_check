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

    public function __construct(Preset $preset)
    {
        $this->preset = $preset;
    }

    public function executeCheck(int $id)
    {

        return $this->preset->getCheckUrl($id);
    }

    protected function fetchPageContent()
    {

    }
}
