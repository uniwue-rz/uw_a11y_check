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
     * @param array $ignoredContentTypes
     * @return array
     */
    public static function getContentElementUidsByPage(int $pageUid, array $ignoredContentTypes = [])
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tt_content');

        $constaints = [];
        $constaints[] = $queryBuilder->expr()->eq(
            'pid',
            $queryBuilder->createNamedParameter($pageUid, Connection::PARAM_INT)
        );

        // Only check sys_language_uid = 0 - @todo: make this configurable
        $constaints[] = $queryBuilder->expr()->eq(
            'sys_language_uid',
            $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
        );

        if (!empty($ignoredContentTypes)) {
            $constaints[] = $queryBuilder->expr()->notIn(
                'CType',
                $queryBuilder->createNamedParameter($ignoredContentTypes, Connection::PARAM_STR_ARRAY)
            );
        }

        $query = $queryBuilder
            ->select('uid')
            ->from('tt_content')
            ->where(...$constaints)
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
