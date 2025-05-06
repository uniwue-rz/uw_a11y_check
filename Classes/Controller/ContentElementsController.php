<?php

namespace UniWue\UwA11yCheck\Controller;

use Exception;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Crypto\HashService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class ContentElementsController
 *
 * Plugin is used to render a given list of content element uids using RECORDS content element
 */
class ContentElementsController extends ActionController
{
    public function showAction(int $pageUid, string $ignoreContentTypes, string $hmac): ResponseInterface
    {
        $hmacString = $pageUid . $ignoreContentTypes;
        $expectedHmac = GeneralUtility::makeInstance(HashService::class)->hmac($hmacString, 'page_content');

        if ($expectedHmac !== $hmac) {
            throw new Exception('HMAC does not match', 1572608738);
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

        return $this->htmlResponse();
    }
}
