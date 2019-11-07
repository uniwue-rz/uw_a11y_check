<?php
namespace UniWue\UwA11yCheck\Check;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use UniWue\UwA11yCheck\Tests\TestInterface;

/**
 * Class TestSuite
 */
class TestSuite
{
    /**
     * @var ObjectStorage
     */
    protected $tests = null;

    /**
     * TestSuite constructor.
     */
    public function __construct()
    {
            $this->tests = new ObjectStorage();
    }

    /**
     * @param TestInterface $test
     */
    public function addTest(TestInterface $test)
    {
        $this->tests->attach($test);
    }

    /**
     * @param TestInterface $test
     */
    public function removeTest(TestInterface $test)
    {
        $this->tests->detach($test);
    }

    /**
     * @return ObjectStorage
     */
    public function getTests()
    {
        return $this->tests;
    }

    /**
     * @param ObjectStorage $tests
     */
    public function setTests(ObjectStorage $tests)
    {
        $this->tests = $tests;
    }
}
