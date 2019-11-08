<?php
namespace UniWue\UwA11yCheck\Tests;

use UniWue\UwA11yCheck\Check\Result;

/**
 * Class AbstractTest
 * @package UniWue\UwA11yCheck\Tests
 */
abstract class AbstractTest implements TestInterface
{
    /**
     * @var string
     */
    protected $id = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $help = '';

    /**
     * @var string
     */
    protected $helpUrl = '';

    /**
     * @var int
     */
    protected $impact = 0;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return htmlspecialchars($this->description);
    }

    /**
     * @return string
     */
    public function getHelp(): string
    {
        return $this->help;
    }

    /**
     * @return string
     */
    public function getHelpUrl(): string
    {
        return $this->helpUrl;
    }

    /**
     * @return int
     */
    public function getImpact(): int
    {
        return $this->impact;
    }

    /**
     * AbstractTest constructor.
     *
     * @param array $configuration
     */
    public function __construct(array $configuration = [])
    {
        $this->configuration = $configuration;
    }

    /**
     * @return Result
     */
    public function initResultWithMetaDataFromTest(): Result
    {
        $result = new Result();
        $result->setTest($this);
        $result->setDescription($this->getDescription());
        $result->setHelp($this->help);
        $result->setHelpUrl($this->helpUrl);
        $result->setImpact($this->impact);
        $result->setStatus(Result\Status::INAPPLICABLE);

        return $result;
    }
}
