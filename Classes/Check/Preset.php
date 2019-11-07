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
     * Returns the check URL
     *
     * @param int $id
     * @return string
     */
    protected function getCheckUrl(int $id): string
    {
        return $this->baseUrl . $this->checkUrlGenerator->getCheckUrl($this->baseUrl, $id);
    }

    /**
     * Executes the testSuite configured in the preset
     *
     * @param int $id
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function executeTestSuite(int $id)
    {
        $checkUrl = $this->getCheckUrl($id);
        $this->analyzer->executeTestSuite($checkUrl, $this->testSuite);
        return $this->analyzer->getResults();
    }
}
