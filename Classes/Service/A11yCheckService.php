<?php
namespace UniWue\UwA11yCheck\Service;

use GuzzleHttp\Client;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

class A11yCheckService
{
    public function executePresetCheck()
    {

    }

    /**
     * @var UriBuilder
     */
    protected $uriBuilder = null;

    /**
     * @param UriBuilder $uriBuilder
     */
    public function injectUriBuilder(UriBuilder $uriBuilder)
    {
        $this->uriBuilder = $uriBuilder;
    }

    public function fetchPageContent(int $uid)
    {
        $uid = 196775; // Text
        $uid = 199937; // Images

        $domain = 'http://uni-wuerzburg-87.typo3.local'; // @todo: Make configurable
        $targetUrl = $domain . $this->getFetchPageLink($uid);

        $client = GeneralUtility::makeInstance(Client::class);
        $response = $client->get($targetUrl);

        // @todo: Check Response for errors
        $body = $response->getBody()->getContents();

        return $body;
    }

    /**
     * @param int $uid
     * @return string
     */
    protected function getFetchPageLink(int $uid)
    {
        // @todo: Determine how to handle translated PIDs
        $contentElementUids = $this->getContentElementsUids($uid);
        $contentElementUidList = implode(',', $contentElementUids);
        $hmac = GeneralUtility::hmac($contentElementUidList, 'tt_content_uid_list');

        return $this->uriBuilder
            ->setTargetPageUid(216467) // @todo: Make configurable
            ->setArguments([
                'tx_uwa11ycheck_pi1[action]' => 'show',
                'tx_uwa11ycheck_pi1[controller]' => 'ContentElements',
                'tx_uwa11ycheck_pi1[uidList]' => $contentElementUidList,
                'tx_uwa11ycheck_pi1[hmac]' => $hmac
            ])
            ->buildFrontendUri();
    }
}