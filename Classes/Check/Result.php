<?php
namespace UniWue\UwA11yCheck\Check;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use UniWue\UwA11yCheck\Check\Result\Node;
use UniWue\UwA11yCheck\Tests\TestInterface;

/**
 * Class Result
 */
class Result
{
    /**
     * @var TestInterface
     */
    protected $test = null;

    /**
     * @var null|int
     */
    protected $status = null;

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
     * @var null|int
     */
    protected $impact = null;

    /**
     * @var ObjectStorage
     */
    protected $nodes = null;

    /**
     * Result constructor.
     */
    public function __construct()
    {
        $this->nodes = new ObjectStorage();
    }

    /**
     * @return TestInterface
     */
    public function getTest(): TestInterface
    {
        return $this->test;
    }

    /**
     * @param TestInterface $test
     */
    public function setTest(TestInterface $test): void
    {
        $this->test = $test;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     */
    public function setStatus(?int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getHelp(): string
    {
        return $this->help;
    }

    /**
     * @param string $help
     */
    public function setHelp(string $help): void
    {
        $this->help = $help;
    }

    /**
     * @return string
     */
    public function getHelpUrl(): string
    {
        return $this->helpUrl;
    }

    /**
     * @param string $helpUrl
     */
    public function setHelpUrl(string $helpUrl): void
    {
        $this->helpUrl = $helpUrl;
    }

    /**
     * @return int|null
     */
    public function getImpact(): ?int
    {
        return $this->impact;
    }

    /**
     * @param int|null $impact
     */
    public function setImpact(?int $impact): void
    {
        $this->impact = $impact;
    }

    /**
     * @return ObjectStorage
     */
    public function getNodes(): ObjectStorage
    {
        return $this->nodes;
    }

    /**
     * @param ObjectStorage $nodes
     */
    public function setNodes(ObjectStorage $nodes): void
    {
        $this->nodes = $nodes;
    }

    /**
     * @param Node $node
     */
    public function addNode(Node $node): void
    {
        $this->nodes->attach($node);
    }

    /**
     * @param Node $node
     */
    public function removeNode(Node $node): void
    {
        $this->nodes->detach($node);
    }

    /**
     * @return bool
     */
    public function getHasErrors(): bool
    {
        return $this->nodes->count() > 0;
    }

    /**
     * Returns the state of the result used in Fluid
     *
     * @return int
     */
    public function getState(): int
    {
        switch ($this->status) {
            case 0:
                $state = 0;
                break;
            case 1:
                if ($this->getImpact() >= 3) {
                    $state = 2;
                } else {
                    $state = 1;
                }
                break;
            case 2:
                $state = -1;
                break;
            default:
                $state = 1;
        }

        return $state;
    }

    /**
     * Returns the title for the check used in Fluid
     *
     * @return string
     */
    public function getTitle(): string
    {
        $status = $this->status ?? 1;
        return LocalizationUtility::translate(
            'LLL:EXT:uw_a11y_check/Resources/Private/Language/locallang_modm1.xlf:result.title.' . $status
        );
    }
}
