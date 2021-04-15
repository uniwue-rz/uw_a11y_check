<?php

namespace UniWue\UwA11yCheck\Check;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use UniWue\UwA11yCheck\Check\Result\Node;

/**
 * Class Result
 */
class Result
{
    /**
     * @var int|null
     */
    protected $status;

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
     * @var int|null
     */
    protected $impact;

    /**
     * @var array
     */
    protected $nodes = [];

    /**
     * @var string
     */
    protected $testId = '';

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
     * @return array
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @param Node $node
     */
    public function addNode(Node $node)
    {
        $this->nodes[] = $node;
    }

    /**
     * @param array $nodes
     */
    public function setNodes(array $nodes): void
    {
        $this->nodes = $nodes;
    }

    /**
     * @return string
     */
    public function getTestId(): string
    {
        return $this->testId;
    }

    /**
     * @param string $testId
     */
    public function setTestId(string $testId): void
    {
        $this->testId = $testId;
    }

    /**
     * @return bool
     */
    public function getHasErrors(): bool
    {
        return count($this->nodes) > 0;
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
            'LLL:EXT:uw_a11y_check/Resources/Private/Language/locallang.xlf:result.title.' . $status
        );
    }
}
