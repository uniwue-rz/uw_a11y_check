<?php
namespace UniWue\UwA11yCheck\CheckUrlGenerators;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use UniWue\UwA11yCheck\Utility\ContentElementUtility;

/**
 * Generates an URL for to configured targetPid.
 * The targetPid must include the pi1 plugin of this extension
 */
class PageContent extends AbstractCheckUrlGenerator
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
     * @var array
     */
    protected $ignoredContentTypes = [];

    /**
     * PageContent constructor.
     *
     * @param array $configuration
     */
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
     *
     * @param string $baseUrl
     * @param int $pageUid
     * @return string|void
     */
    public function getCheckUrl(string $baseUrl, int $pageUid): string
    {
        $ignoreContentTypes = implode(',', $this->ignoredContentTypes);
        $hmacString = $pageUid . $ignoreContentTypes;
        $hmac = GeneralUtility::hmac($hmacString, 'page_content');

        return $this->uriBuilder
            ->setTargetPageUid($this->targetPid)
            ->setArguments([
                'tx_uwa11ycheck_pi1[action]' => 'show',
                'tx_uwa11ycheck_pi1[controller]' => 'ContentElements',
                'tx_uwa11ycheck_pi1[pageUid]' => $pageUid,
                'tx_uwa11ycheck_pi1[ignoreContentTypes]' => $ignoreContentTypes,
                'tx_uwa11ycheck_pi1[hmac]' => $hmac
            ])
            ->buildFrontendUri();
    }
}
