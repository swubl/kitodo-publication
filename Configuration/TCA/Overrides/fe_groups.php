<?php
defined('TYPO3_MODE') or die();

$temporaryColumns = array (
    'kitodo_role' => array (
        'exclude' => 0,
        'label' => 'examples_options',
        'config' => array (
        'type' => 'select',
        'items' => array (
            array('Forschender', '1'),
            array('Bibliothekar', '2'),
        ),
            'size' => 1,
            'maxitems' => 1,
        )
    ),
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'fe_groups',
    $temporaryColumns
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'fe_groups',
    'kitodo_role'
);