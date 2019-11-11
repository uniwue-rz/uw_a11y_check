<?php
namespace UniWue\UwA11yCheck\Analyzers;

use UniWue\UwA11yCheck\Check\Preset;
use UniWue\UwA11yCheck\Check\ResultSet;

/**
 * Interface AnalyzerInterface
 */
interface AnalyzerInterface
{
    public function getType();

    public function runTests(Preset $preset, int $recordUid): ResultSet;

    public function initializePageUids(int $recordUid, int $levels): void;

    public function getCheckRecordUids(Preset $preset): array;
}
