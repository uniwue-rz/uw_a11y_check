<?php

defined('TYPO3_MODE') or die();

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use UniWue\UwA11yCheck\Controller\ContentElementsController;
use UniWue\UwA11yCheck\Property\TypeConverter\PresetTypeConverter;

call_user_func(function () {
    ExtensionUtility::configurePlugin(
        'UwA11yCheck',
        'Pi1',
        [
            ContentElementsController::class => 'show',
        ],
        // non-cacheable actions
        [
            ContentElementsController::class => 'show',
        ]
    );

    ExtensionUtility::registerTypeConverter(PresetTypeConverter::class);

    // Register configuration yaml file
    if (empty($GLOBALS['TYPO3_CONF_VARS']['UwA11yCheck']['Configuration'])) {
        $GLOBALS['TYPO3_CONF_VARS']['UwA11yCheck']['Configuration'] = 'EXT:uw_a11y_check/Configuration/A11y/Default.yaml';
    }

    if (!Environment::isComposerMode()) {
        $composerAutoloadFile = ExtensionManagementUtility::extPath('uw_a11y_check')
            . 'Resources/Private/Php/vendor/autoload.php';
        require_once($composerAutoloadFile);
    }
});
