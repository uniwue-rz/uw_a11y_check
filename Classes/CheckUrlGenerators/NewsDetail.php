<?php

namespace UniWue\UwA11yCheck\CheckUrlGenerators;

use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Generates an URL for to configured targetPid.
 * The targetPid must include the pi1 plugin of ext:news and the plugin must show the detail view
 */
class NewsDetail extends AbstractCheckUrlGenerator
{
    protected array $requiredConfiguration = ['targetPid'];
    protected int $targetPid = 0;

    /**
     * NewsDetail constructor.
     */
    public function __construct(array $configuration)
    {
        parent::__construct($configuration);

        $this->tableName = 'tx_news_domain_model_news';
        $this->editRecordTable = 'tx_news_domain_model_news';
        $this->targetPid = $configuration['targetPid'];
    }

    /**
     * Returns the check URL
     */
    public function getCheckUrl(int $newsUid): string
    {
        $arguments = [
            'tx_news_pi1[action]' => 'detail',
            'tx_news_pi1[controller]' => 'News',
            'tx_news_pi1[news]' => $newsUid,
        ];
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($this->targetPid);

        return $site->getRouter()->generateUri((string)$this->targetPid, $arguments);
    }
}
