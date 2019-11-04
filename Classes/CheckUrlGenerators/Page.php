<?php
namespace UniWue\UwA11yCheck\CheckUrlGenerators;

/**
 * Generates an URL for to configured targetPid.
 */
class Page extends AbstractCheckUrlGenerator
{
    /**
     * Returns the check URL
     *
     * @param string $baseUrl
     * @param int $pageUid
     * @return string|void
     */
    public function getCheckUrl(string $baseUrl, int $pageUid)
    {
        return $this->uriBuilder
            ->setTargetPageUid($pageUid)
            ->buildFrontendUri();
    }
}
