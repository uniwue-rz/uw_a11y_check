<?php
namespace UniWue\UwA11yCheck\Analyzers;

use UniWue\UwA11yCheck\Check\Preset;

/**
 * Class PageContent
 */
class PageContentAnalyzer extends AbstractAnalyzer
{
    /**
     * @var string
     */
    protected $type = AbstractAnalyzer::TYPE_INTERNAL;

    /**
     * Return an aray of page record Uids to check
     *
     * @param Preset $preset
     * @return array
     */
    public function getCheckRecordUids(Preset $preset): array
    {
        return $this->pageUids;
    }
}
