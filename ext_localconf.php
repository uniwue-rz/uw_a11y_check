<?php
defined('TYPO3_MODE') or die();

call_user_func(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'UniWue.uw_a11y_check',
        'Pi1',
        [
            'ContentElements' => 'show',
        ],
        // non-cacheable actions
        [
            'ContentElements' => 'show',
        ]
    );

    if (TYPO3_MODE === 'BE') {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter(
            \UniWue\UwA11yCheck\Property\TypeConverter\PresetTypeConverter::class
        );
    }

    // Register configuration yaml file
    if (empty($GLOBALS['TYPO3_CONF_VARS']['UwA11yCheck']['Configuration'])) {
        $GLOBALS['TYPO3_CONF_VARS']['UwA11yCheck']['Configuration'] =
            'EXT:uw_a11y_check/Configuration/A11y/Default.yaml';
    }
});
