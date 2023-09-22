<?php

namespace UniWue\UwA11yCheck\Check\Result;

/**
 * Class Node
 */
class Node
{
    protected string $html = '';
    protected ?Impact $impact;
    protected string $target = '';
    protected int $uid = 0;

    public function getHtml(): string
    {
        return $this->html;
    }

    public function setHtml(string $html): void
    {
        $this->html = $html;
    }

    public function getImpact(): ?Impact
    {
        return $this->impact;
    }

    public function setImpact(?Impact $impact): void
    {
        $this->impact = $impact;
    }

    public function getTarget(): string
    {
        // @extensionScannerIgnoreLine False positive
        return $this->target;
    }

    public function setTarget(string $target): void
    {
        // @extensionScannerIgnoreLine False positive
        $this->target = $target;
    }

    public function getUid(): int
    {
        return $this->uid;
    }

    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }
}
