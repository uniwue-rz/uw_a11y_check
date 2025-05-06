<?php

return [
    'web_UwA11yCheckTxUwa11ycheckM1' => [
        'parent' => 'web',
        'access' => 'user',
        'labels' => 'LLL:EXT:uw_a11y_check/Resources/Private/Language/locallang_modm1.xlf',
        'extensionName' => 'UwA11yCheck',
        'iconIdentifier' => 'ext-uwa11ycheck-default',
        'controllerActions' => [
            'UniWue\UwA11yCheck\Controller\A11yCheckController' => [
                'index',
                'check',
                'results',
                'acknowledgeResult',
            ],
        ],
    ],
];
