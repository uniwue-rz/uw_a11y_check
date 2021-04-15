<?php

defined('TYPO3_MODE') or die();

call_user_func(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'uw_a11y_check',
        'Pi1',
        [
            \UniWue\UwA11yCheck\Controller\ContentElementsController::class => 'show',
        ],
        // non-cacheable actions
        [
            \UniWue\UwA11yCheck\Controller\ContentElementsController::class => 'show',
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

    $composerAutoloadFile = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('uw_a11y_check')
        . 'Resources/Private/Php/vendor/autoload.php';
    require_once($composerAutoloadFile);
});
