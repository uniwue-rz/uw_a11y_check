<?php

namespace UniWue\UwA11yCheck\Analyzers;

use UniWue\UwA11yCheck\Check\Preset;

/**
 * Class PageContent
 */
class PageContentAnalyzer extends AbstractAnalyzer
{
    protected string $type = AbstractAnalyzer::TYPE_INTERNAL;

    /**
     * Return an aray of page record Uids to check
     */
    public function getCheckRecordUids(Preset $preset): array
    {
        return $this->pageUids;
    }
}
