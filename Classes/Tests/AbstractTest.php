<?php
namespace UniWue\UwA11yCheck\Tests;

use UniWue\UwA11yCheck\Check\Result;
use UniWue\UwA11yCheck\Utility\Tests\ElementUtility;

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

        if ($this->description === '') {
            $this->description = $this->translateLabel($this->id . '.description');
        }
        if ($this->help === '') {
            $this->help = $this->translateLabel($this->id . '.help');
        }
    }

    /**
     * @return Result
     */
    public function initResultWithMetaDataFromTest(): Result
    {
        $result = new Result();
        $result->setTestId($this->getId());
        $result->setDescription($this->getDescription());
        $result->setHelp($this->help);
        $result->setHelpUrl($this->helpUrl);
        $result->setImpact($this->impact);
        $result->setStatus(Result\Status::INAPPLICABLE);

        return $result;
    }

    /**
     * Returns the element UID in the node. If no UID could be determined, the fallbackElementUid is returned
     *
     * @param \DOMElement $node
     * @param int $fallbackElementUid
     * @return int
     */
    public function getElementUid(\DOMElement $node, int $fallbackElementUid): int
    {
        $elementUid = ElementUtility::determineElementUid($node);
        if ($elementUid === 0) {
            $elementUid = $fallbackElementUid;
        }
        return $elementUid;
    }

    /**
     * @param string $label
     * @return string
     */
    protected function translateLabel(string $label): string
    {
        $langId = 'LLL:EXT:uw_a11y_check/Resources/Private/Language/locallang.xlf:';
        return $this->getLanguageService()->sL($langId . $label);
    }

    /**
     * Returns LanguageService
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
