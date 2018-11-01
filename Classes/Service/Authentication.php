<?php

namespace Wazum\ContextAuth\Service;

use TYPO3\CMS\Core\Authentication\AbstractUserAuthentication;
use TYPO3\CMS\Sv\AuthenticationService;

/**
 * Class Authentication
 *
 * @package Wazum\ContextAuth\Service
 * @author Wolfgang Klinger <wolfgang@wazum.com>
 */
class Authentication extends AuthenticationService
{
    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * Initialize authentication service
     *
     * @param string $mode Subtype of the service which is used to call the service.
     * @param array $loginData
     * @param array $authInfo
     * @param AbstractUserAuthentication $parent Parent object
     */
    public function initAuth($mode, $loginData, $authInfo, $parent)
    {
        parent::initAuth($mode, $loginData, $authInfo, $parent);

        if ($this->shouldAuthenticate($loginData)) {
            $this->enabled = true;
        }
    }

    /**
     * @param array $loginData
     * @param string $passwordTransmissionStrategy
     * @return int
     */
    public function processLoginData(array &$loginData, $passwordTransmissionStrategy): int
    {
        return $this->shouldAuthenticate($loginData) ? 200 : 100;
    }

    /**
     * @return array|null
     */
    public function getUser(): ?array
    {
        $user = null;
        if ($this->enabled) {
            $username = trim($this->getServiceOption('username'));
            $user = $this->fetchUserRecord($username);
        }

        return $user;
    }

    /**
     * @param array $user
     * @return int
     */
    public function authUser(array $user): int
    {
        if ($this->enabled) {
            $this->writelog(255, 3, 3, 1, 'Login attempt from %s (%s), username \'%s\' succeeded based on context',
                [$this->authInfo['REMOTE_ADDR'], $this->authInfo['REMOTE_HOST'], $user['username']]);

            return 200;
        }

        return 100;
    }

    /**
     * @param array $loginData
     * @return bool
     */
    protected function shouldAuthenticate(array $loginData): bool
    {
        return (bool)$this->getServiceOption('enable') &&
            // Backend login
            $loginData['status'] === 'login' &&
            // No credentials (username, password) given
            $this->noCredentialsProvided($loginData) &&
            // Correct sub context (e.g. 'Docker') set
            $this->isRequiredContext();
    }

    /**
     * @param array $loginData
     * @return bool
     */
    protected function noCredentialsProvided(array $loginData): bool
    {
        return empty($loginData['uname']) && empty($loginData['uident']);
    }

    /**
     * Returns true if the context matches 'Development(/*)'
     *
     * @return bool
     */
    protected function isRequiredContext(): bool
    {
        $context = null;
        $requiredContext = trim($this->getServiceOption('context'));
        if (!empty($requiredContext)) {
            // Extract the sub context value
            [$context, $subContext] = explode('/', \TYPO3\CMS\Core\Utility\GeneralUtility::getApplicationContext());

            if (!empty($subContext)) {
                return "$context/$subContext" === "Development/$requiredContext";
            }
        }

        return $context === 'Development';
    }
}
