<?php

namespace UniWue\UwA11yCheck\Check;

use UniWue\UwA11yCheck\Tests\TestInterface;

/**
 * Class TestSuite
 */
class TestSuite
{
    protected array $tests = [];

    public function addTest(TestInterface $test): void
    {
        $this->tests[] = $test;
    }

    public function getTests(): array
    {
        return $this->tests;
    }

    public function setTests(array $tests): void
    {
        $this->tests = $tests;
    }
}
