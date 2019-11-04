<?php
defined('TYPO3_MODE') or die();

/**
 * Plugins
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'uw_a11y_check',
    'Pi1',
    'Display content elements for a11y check'
);

/**
 * Remove unused fields
 */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['uwa11ycheck_pi1'] =
    'layout,recursive,select_key,pages';
