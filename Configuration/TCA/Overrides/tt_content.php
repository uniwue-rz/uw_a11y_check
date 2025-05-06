<?php

defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Plugins
 */
ExtensionUtility::registerPlugin(
    'uw_a11y_check',
    'Pi1',
    'Display content elements for a11y check'
);
