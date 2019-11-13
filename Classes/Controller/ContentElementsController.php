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
     * @param int $pageUid
     * @param string $ignoreContentTypes
     * @param string $hmac
     * @throws \Exception
     */
    public function showAction(int $pageUid, string $ignoreContentTypes, string $hmac)
    {
        $hmacString = $pageUid . $ignoreContentTypes;
        $expectedHmac = GeneralUtility::hmac($hmacString, 'page_content');

        if ($expectedHmac !== $hmac) {
            throw new \Exception('HMAC does not match', 1572608738828);
        }

        $whereCondition = 'colPos >= 0';

        if ($ignoreContentTypes !== '') {
            $ignoreContentTypes = preg_replace('#[^a-zA-Z_,]#', '', $ignoreContentTypes);
            $ignoreContentTypesArray = GeneralUtility::trimExplode(',', $ignoreContentTypes);
            $additionalWhere = '"' . implode('","', $ignoreContentTypesArray) . '"';
            $whereCondition .= ' AND CType not in(' . $additionalWhere . ')';
        }

        $this->view->assignMultiple([
            'pageUid' => $pageUid,
            'where' => $whereCondition,
        ]);
    }
}
