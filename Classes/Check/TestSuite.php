<?php

namespace UniWue\UwA11yCheck\Check;

use UniWue\UwA11yCheck\Tests\TestInterface;

/**
 * Class TestSuite
 */
class TestSuite
{
    /**
     * @var array
     */
    protected $tests = [];

    /**
     * @param TestInterface $test
     */
    public function addTest(TestInterface $test)
    {
        $this->tests[] = $test;
    }

    /**
     * @return array
     */
    public function getTests()
    {
        return $this->tests;
    }

    /**
     * @param array $tests
     */
    public function setTests(array $tests)
    {
        $this->tests = $tests;
    }
}
