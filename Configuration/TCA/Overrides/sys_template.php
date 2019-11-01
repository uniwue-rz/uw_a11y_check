<?php
defined('TYPO3_MODE') or die();

/**
 * Default TypoScript
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'uw_a11y_check',
    'Configuration/TypoScript',
    'Uni Würzburg - Accessibility Check'
);
