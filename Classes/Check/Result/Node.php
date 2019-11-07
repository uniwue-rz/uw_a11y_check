<?php
namespace UniWue\UwA11yCheck\Check\Result;

/**
 * Class Node
 */
class Node
{
    /**
     * @var string
     */
    protected $html = '';

    /**
     * @var null|Impact
     */
    protected $impact = null;

    /**
     * @var string
     */
    protected $target = '';

    /**
     * @var int
     */
    protected $uid = 0;

    /**
     * @return string
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * @param string $html
     */
    public function setHtml(string $html): void
    {
        $this->html = $html;
    }

    /**
     * @return Impact|null
     */
    public function getImpact(): ?Impact
    {
        return $this->impact;
    }

    /**
     * @param Impact|null $impact
     */
    public function setImpact(?Impact $impact): void
    {
        $this->impact = $impact;
    }

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @param string $target
     */
    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    /**
     * @return int
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    /**
     * @param int $uid
     */
    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }
}
