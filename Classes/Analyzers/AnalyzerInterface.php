<?php
namespace UniWue\UwA11yCheck\Analyzers;

use UniWue\UwA11yCheck\Check\TestSuite;

/**
 * Interface AnalyzerInterface
 */
interface AnalyzerInterface
{
    public function getType();

    public function executeTestSuite(string $url, TestSuite $testSuite);
}
