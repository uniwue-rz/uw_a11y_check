<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Default TypoScript
 */
ExtensionManagementUtility::addStaticFile(
    'uw_a11y_check',
    'Configuration/TypoScript',
    'TYPO3 Accessibility Check'
);
