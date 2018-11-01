<?php

defined('TYPO3_MODE') or die('Access denied.');

(static function ($extensionKey) {
    $configuration = [];
    try {
        $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get($extensionKey);
    } catch (\TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException $e) {
    } catch (\TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException $e) {
    }

    if (isset($configuration['enable']) && (bool)$configuration['enable'] &&
        !empty($configuration['username'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SVCONF']['auth']['contextAuthenticationService'] = $configuration;

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
            $extensionKey,
            'auth',
            'contextAuthenticationService',
            [
                'title' => 'Context authentication',
                'description' => 'Automatic backend authentication based on context',
                'subtype' => 'processLoginDataBE,getUserBE,authUserBE',
                'available' => true,
                'priority' => 90,
                'quality' => 50,
                'os' => '',
                'exec' => '',
                'className' => \Wazum\ContextAuth\Service\Authentication::class
            ]
        );

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['loginProviders'][1541091224] = [
            'provider' => \Wazum\ContextAuth\LoginProvider\ContextLoginProvider::class,
            'icon-class' => 'fa-context',
            'sorting' => 200,
            'label' => 'LLL:EXT:context_auth/Resources/Private/Language/locallang.xlf:login.link'
        ];
        $GLOBALS['TYPO3_CONF_VARS']['BE']['showRefreshLoginPopup'] = false;
    }
})('context_auth');
