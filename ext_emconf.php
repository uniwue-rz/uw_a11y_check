<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'a11y check',
    'description' => 'Configurable a11y check for tt_content and extension records',
    'category' => 'fe',
    'author' => 'Torben Hansen on behalf of Universität Würzburg',
    'author_email' => 'torben@derhansen.com',
    'state' => 'stable',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'version' => '4.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
