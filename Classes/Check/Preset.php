<?php
namespace UniWue\UwA11yCheck\Check;

use UniWue\UwA11yCheck\Analyzers\AbstractAnalyzer;
use UniWue\UwA11yCheck\CheckUrlGenerators\AbstractCheckUrlGenerator;

/**
 * Class Preset
 */
class Preset
{
    /**
     * @var string
     */
    protected $id = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $baseUrl = '';

    /**
     * @var AbstractAnalyzer
     */
    protected $analyzer = null;

    /**
     * @var AbstractCheckUrlGenerator
     */
    protected $checkUrlGenerator = null;

    /**
     * @var TestSuite
     */
    protected $testSuite = null;

    /**
     * Preset constructor.
     *
     * @param string $id
     * @param string $name
     * @param AbstractAnalyzer $analyzer
     * @param AbstractCheckUrlGenerator $checkUrlGenerator
     * @param TestSuite $testSuite
     * @param array $configuration
     */
    public function __construct(
        string $id,
        string $name,
        AbstractAnalyzer $analyzer,
        AbstractCheckUrlGenerator $checkUrlGenerator,
        TestSuite $testSuite,
        array $configuration
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->analyzer = $analyzer;
        $this->checkUrlGenerator = $checkUrlGenerator;
        $this->baseUrl = $configuration['baseUrl'];
        $this->testSuite = $testSuite;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getCheckTableName(): string
    {
        return $this->checkUrlGenerator->getTableName();
    }

    /**
     * @return string
     */
    public function getEditRecordTableName(): string
    {
        return $this->checkUrlGenerator->getEditRecordTable();
    }

    /**
     * Returns the check URL
     *
     * @param int $id
     * @return string
     */
    public function getCheckUrl(int $id): string
    {
        return $this->checkUrlGenerator->getCheckUrl($this->baseUrl, $id);
    }

    /**
     * @return TestSuite
     */
    public function getTestSuite(): TestSuite
    {
        return $this->testSuite;
    }

    /**
     * Executes the testSuite configured in the preset by the given page UID and recursive levels
     *
     * @param int $pageUid
     * @param int $levels
     * @return array
     */
    public function executeTestSuiteByPageUid(int $pageUid, int $levels)
    {
        $result = [];
        $this->analyzer->initializePageUids($pageUid, $levels);

        foreach ($this->analyzer->getCheckRecordUids($this) as $recordUid) {
            $result[] = $this->analyzer->runTests($this, $recordUid);
        }

        return $result;
    }

    /**
     * Executes the testSuite configured in the preset by the given array of record UIDs
     *
     * @param array $recordUids
     * @return array
     */
    public function executeTestSuiteByRecordUids(array $recordUids): array
    {
        $result = [];

        foreach ($recordUids as $recordUid) {
            $result[] = $this->analyzer->runTests($this, $recordUid);
        }

        return $result;
    }
}
