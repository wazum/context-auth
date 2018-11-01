<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Context Backend Authentication',
    'description' => 'Automatic backend authentication if TYPO3_CONTEXT matches',
    'category' => 'backend',
    'author' => 'Wolfgang Klinger',
    'author_email' => 'wolfgang@wazum.com',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'author_company' => 'wazum.com',
    'version' => '1.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.5-9.5.99',
        ]
    ]
];
