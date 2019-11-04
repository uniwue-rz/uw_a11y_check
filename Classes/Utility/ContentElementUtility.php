<?php
namespace UniWue\UwA11yCheck\Utility;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ContentElementUtility
 */
class ContentElementUtility
{
    /**
     * Returns an array of content element UIDs for the given page uid
     *
     * @param int $pageUid
     * @return array
     */
    public static function getContentElementsUidsByPage(int $pageUid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tt_content');

        $query = $queryBuilder
            ->select('uid')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($pageUid, Connection::PARAM_INT)
                )
            )
            ->addOrderBy('sorting', 'ASC')
            ->addOrderBy('colPos', 'ASC');

        // @todo: Consider to define exclude colPos

        $queryResult = $query->execute()->fetchAll();

        $uidList = [];
        foreach ($queryResult as $record) {
            $uidList[] = $record['uid'];
        }

        return $uidList;
    }
}
