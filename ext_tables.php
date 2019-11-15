<?php
defined('TYPO3_MODE') or die();

call_user_func(function () {
    if (TYPO3_MODE === 'BE') {
        /**
         * Register Administration Module
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'UniWue.uw_a11y_check',
            'web',
            'tx_uwa11ycheck_m1',
            '',
            [
                'A11yCheck' => 'index,check,results,acknowledgeResult',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:uw_a11y_check/Resources/Public/Icons/a11y_check.svg',
                'labels' => 'LLL:EXT:uw_a11y_check/Resources/Private/Language/locallang_modm1.xlf',
            ]
        );
    }
});
