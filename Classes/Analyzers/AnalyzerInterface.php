<?php
namespace UniWue\UwA11yCheck\Analyzers;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use UniWue\UwA11yCheck\Check\TestSuite;

/**
 * Interface AnalyzerInterface
 */
interface AnalyzerInterface
{
    public function getType();

    public function executeTestSuite(TestSuite $testSuite, string $url): ObjectStorage;
}
