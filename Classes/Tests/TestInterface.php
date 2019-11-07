<?php
namespace UniWue\UwA11yCheck\Tests;

use UniWue\UwA11yCheck\Check\Result;

/**
 * Interface TestInterface
 */
interface TestInterface
{
    public function run(string $html): Result;
}
