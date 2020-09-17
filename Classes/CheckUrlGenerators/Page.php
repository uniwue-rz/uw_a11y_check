<?php
namespace UniWue\UwA11yCheck\CheckUrlGenerators;

/**
 * Generates an URL for to configured targetPid.
 */
class Page extends AbstractCheckUrlGenerator
{
    /**
     * Page constructor.
     *
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        parent::__construct($configuration);

        $this->tableName = 'pages';
        $this->editRecordTable = 'pages';
    }

    /**
     * Returns the check URL
     *
     * @param string $baseUrl
     * @param int $pageUid
     * @return string|void
     */
    public function getCheckUrl(string $baseUrl, int $pageUid): string
    {
        return $this->uriBuilder
            ->setTargetPageUid($pageUid)
            ->buildFrontendUri();
    }
}
