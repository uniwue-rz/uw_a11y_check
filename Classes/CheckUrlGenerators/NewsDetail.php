<?php
namespace UniWue\UwA11yCheck\CheckUrlGenerators;

/**
 * Generates an URL for to configured targetPid.
 * The targetPid must include the pi1 plugin of ext:news and the plugin must show the detail view
 */
class NewsDetail extends AbstractCheckUrlGenerator
{
    /**
     * @var array
     */
    protected $requiredConfiguration = ['targetPid'];

    /**
     * @var int
     */
    protected $targetPid = 0;

    /**
     * NewsDetail constructor.
     *
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        parent::__construct($configuration);

        $this->tableName = 'tx_news_domain_model_news';
        $this->targetPid = $configuration['targetPid'];
    }

    /**
     * Returns the check URL
     *
     * @param string $baseUrl
     * @param int $newsUid
     * @return string|void
     */
    public function getCheckUrl(string $baseUrl, int $newsUid)
    {
        return $this->uriBuilder
            ->setTargetPageUid($this->targetPid)
            ->setArguments([
                'tx_news_pi1[action]' => 'detail',
                'tx_news_pi1[controller]' => 'News',
                'tx_news_pi1[news]' => $newsUid,
            ])
            ->buildFrontendUri();
    }
}
