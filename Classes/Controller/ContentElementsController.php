<?php
namespace UniWue\UwA11yCheck\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Security\Cryptography\HashService;

/**
 * Class ContentElementsController
 *
 * Plugin is used to render a given list of content element uids using RECORDS content element
 */
class ContentElementsController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var HashService
     */
    protected $hashService = null;

    /**
     * @param HashService $hashService
     */
    public function injectHashService(\TYPO3\CMS\Extbase\Security\Cryptography\HashService $hashService)
    {
        $this->hashService = $hashService;
    }

    /**
     * @param string $uidList
     * @param string $hmac
     */
    public function showAction(string $uidList, $hmac)
    {
        $expectedHmac = GeneralUtility::hmac($uidList, 'tt_content_uid_list');
        if ($expectedHmac !== $hmac) {
            throw new \Exception('HMAC does not match', 1572608738828);
        }

        $this->view->assignMultiple([
            'ttContentUidList' => GeneralUtility::intExplode(',', $uidList, true)
        ]);
    }
}
