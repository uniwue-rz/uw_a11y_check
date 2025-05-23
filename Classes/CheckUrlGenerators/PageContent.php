<?php

namespace UniWue\UwA11yCheck\CheckUrlGenerators;

use TYPO3\CMS\Core\Crypto\HashService;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Generates an URL for to configured targetPid.
 * The targetPid must include the pi1 plugin of this extension
 */
class PageContent extends AbstractCheckUrlGenerator
{
    protected array $requiredConfiguration = ['targetPid'];
    protected int $targetPid = 0;
    protected array $ignoredContentTypes = [];

    public function __construct(array $configuration)
    {
        parent::__construct($configuration);

        $this->tableName = 'pages';
        $this->editRecordTable = 'tt_content';
        $this->targetPid = $configuration['targetPid'];

        if (isset($configuration['ignoreContentTypes']) && is_array($configuration['ignoreContentTypes'])) {
            $this->ignoredContentTypes = $configuration['ignoreContentTypes'];
        }
    }

    /**
     * Returns the check URL
     */
    public function getCheckUrl(int $pageUid): string
    {
        $ignoreContentTypes = implode(',', $this->ignoredContentTypes);
        $hmacString = $pageUid . $ignoreContentTypes;
        $hmac = GeneralUtility::makeInstance(HashService::class)->hmac($hmacString, 'page_content');

        $arguments = [
            'tx_uwa11ycheck_pi1[action]' => 'show',
            'tx_uwa11ycheck_pi1[controller]' => 'ContentElements',
            'tx_uwa11ycheck_pi1[pageUid]' => $pageUid,
            'tx_uwa11ycheck_pi1[ignoreContentTypes]' => $ignoreContentTypes,
            'tx_uwa11ycheck_pi1[hmac]' => $hmac,
        ];
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($this->targetPid);

        return $site->getRouter()->generateUri((string)$this->targetPid, $arguments);
    }
}
