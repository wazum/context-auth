<?php
declare(strict_types=1);

namespace Wazum\ContextAuth\LoginProvider;

use TYPO3\CMS\Backend\Controller\LoginController;
use TYPO3\CMS\Backend\LoginProvider\LoginProviderInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class ContextLoginProvider
 *
 * @package Wazum\ContextAuth\LoginProvider
 * @author Wolfgang Klinger <wolfgang@wazum.com>
 */
class ContextLoginProvider implements LoginProviderInterface
{
    /**
     * @param StandaloneView $view
     * @param PageRenderer $pageRenderer
     * @param LoginController $loginController
     */
    public function render(StandaloneView $view, PageRenderer $pageRenderer, LoginController $loginController): void
    {
        $configuration = [];
        try {
            $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('context_auth');
        } catch (\TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException $e) {
        } catch (\TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException $e) {
        }
        if (!empty($configuration['username'])) {
            $view->assign('username', trim($configuration['username']));
            $view->assign('context', (string)GeneralUtility::getApplicationContext());
            $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:context_auth/Resources/Private/Templates/Backend/Login.html'));
        }
    }
}

