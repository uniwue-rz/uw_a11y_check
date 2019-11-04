<?php
namespace UniWue\UwA11yCheck\Check;

use UniWue\UwA11yCheck\Analyzers\AbstractAnalyzer;
use UniWue\UwA11yCheck\CheckUrlGenerators\AbstractCheckUrlGenerator;

/**
 * Class Preset
 */
class Preset
{
    protected $id = '';
    protected $name = '';
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
     * Preset constructor.
     *
     * @param string $id
     * @param string $name
     * @param AbstractAnalyzer $analyzer
     * @param AbstractCheckUrlGenerator $checkUrlGenerator
     * @param array $configuration
     */
    public function __construct(
        string $id,
        string $name,
        AbstractAnalyzer $analyzer,
        AbstractCheckUrlGenerator $checkUrlGenerator,
        array $configuration
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->analyzer = $analyzer;
        $this->checkUrlGenerator = $checkUrlGenerator;
        $this->baseUrl = $configuration['baseUrl'];
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the check URL
     *
     * @param int $id
     * @return string
     */
    public function getCheckUrl(int $id): string
    {
        return $this->baseUrl . $this->checkUrlGenerator->getCheckUrl($this->baseUrl, $id);
    }
}
