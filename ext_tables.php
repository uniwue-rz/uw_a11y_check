<?php

defined('TYPO3_MODE') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use UniWue\UwA11yCheck\Controller\A11yCheckController;

call_user_func(static function () {
    /**
     * Register A11Y Module
     */
    ExtensionUtility::registerModule(
        'UwA11yCheck',
        'web',
        'tx_uwa11ycheck_m1',
        '',
        [
            A11yCheckController::class => 'index,check,results,acknowledgeResult',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:uw_a11y_check/Resources/Public/Icons/Extension.svg',
            'labels' => 'LLL:EXT:uw_a11y_check/Resources/Private/Language/locallang_modm1.xlf',
        ]
    );
});
