<?php

namespace UniWue\UwA11yCheck\Domain\Model\Dto;

use UniWue\UwA11yCheck\Check\Preset;

/**
 * Class CheckDemand
 */
class CheckDemand
{
    protected string $analyze = '';
    protected ?Preset $preset;
    protected int $level = 0;

    public function getAnalyze(): string
    {
        return $this->analyze;
    }

    public function setAnalyze(string $analyze): void
    {
        $this->analyze = $analyze;
    }

    public function getPreset(): ?Preset
    {
        return $this->preset;
    }

    public function setPreset(?Preset $preset): void
    {
        $this->preset = $preset;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function toArray(): array
    {
        return [
            'analyze' => $this->getAnalyze(),
            'preset' => $this->preset->getId() ?? '',
            'level' => $this->getLevel(),
        ];
    }
}
