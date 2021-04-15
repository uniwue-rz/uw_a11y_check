<?php

namespace UniWue\UwA11yCheck\Domain\Model\Dto;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use UniWue\UwA11yCheck\Check\Preset;

/**
 * Class CheckDemand
 */
class CheckDemand extends AbstractEntity
{
    /**
     * @var string
     */
    protected $analyze = '';

    /**
     * @var \UniWue\UwA11yCheck\Check\Preset|null
     */
    protected $preset;

    /**
     * @var int
     */
    protected $level = 0;

    /**
     * @return string
     */
    public function getAnalyze(): string
    {
        return $this->analyze;
    }

    /**
     * @param string $analyze
     */
    public function setAnalyze(string $analyze): void
    {
        $this->analyze = $analyze;
    }

    /**
     * @return \UniWue\UwA11yCheck\Check\Preset|null
     */
    public function getPreset(): ?Preset
    {
        return $this->preset;
    }

    /**
     * @param \UniWue\UwA11yCheck\Check\Preset|null $preset
     */
    public function setPreset(?Preset $preset): void
    {
        $this->preset = $preset;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'analyze' => $this->getAnalyze(),
            'preset' => $this->preset->getId() ?? '',
            'level' => $this->getLevel(),
        ];
    }
}
