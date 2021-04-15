<?php

namespace UniWue\UwA11yCheck\CheckUrlGenerators;

use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
     * @param int $pageUid
     * @return string|void
     */
    public function getCheckUrl(int $pageUid): string
    {
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($pageUid);

        return $site->getRouter()->generateUri((string)$pageUid);
    }
}
