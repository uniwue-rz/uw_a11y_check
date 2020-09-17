<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'a11y check',
    'description' => 'Configurable a11y check for tt_content and extension records',
    'category' => 'fe',
    'author' => 'Torben Hansen',
    'author_email' => 'torben@derhansen.com',
    'state' => 'beta',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
            'php' => '7.0.0-7.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
