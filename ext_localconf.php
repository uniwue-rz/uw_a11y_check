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
});
