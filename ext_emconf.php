<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'a11y check',
    'description' => 'Configurable a11y check for tt_content and extension records',
    'category' => 'fe',
    'author' => 'Torben Hansen on behalf of Universität Würzburg',
    'author_email' => 'torben@derhansen.com',
    'state' => 'stable',
    'version' => '5.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
