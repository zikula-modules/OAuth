<?php
/**
 * This file is part of the Zikula package.
 *
 * Copyright Zikula Foundation - http://zikula.org/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\OAuthModule\AuthenticationMethod;

use League\OAuth2\Client\Provider\Google;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Zikula\OAuthModule\Exception\InvalidProviderConfigException;

class GoogleAuthenticationMethod extends AbstractAuthenticationMethod
{
    public function getDisplayName()
    {
        return 'Google';
    }

    public function getDescription()
    {
        return 'Login using Google via OAuth.';
    }

    protected function getUserName()
    {
        return $this->user->getName();
    }

    protected function getProvider()
    {
        $settings = $this->variableApi->get('ZikulaOAuthModule', 'google');
        if (!isset($settings['id']) || !isset($settings['secret'])) {
            throw new InvalidProviderConfigException('Invalid settings for Google OAuth provider.');
        }

        return new Google([
            'clientId' => $settings['id'],
            'clientSecret' => $settings['secret'],
            'redirectUri' => $this->router->generate('zikulausersmodule_access_login', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    }
}
