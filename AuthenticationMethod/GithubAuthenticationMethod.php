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

use League\OAuth2\Client\Provider\Github;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Zikula\OAuthModule\Exception\InvalidProviderConfigException;

class GithubAuthenticationMethod extends AbstractAuthenticationMethod
{
    public function getDisplayName()
    {
        return 'Github';
    }

    public function getDescription()
    {
        return 'Login using Github via OAuth.';
    }

    protected function getUserName()
    {
        return $this->user->getNickname();
    }

    protected function getProvider()
    {
        $settings = $this->variableApi->get('ZikulaOAuthModule', 'github');
        if (!isset($settings['id']) || !isset($settings['secret'])) {
            throw new InvalidProviderConfigException('Invalid settings for Github OAuth provider.');
        }

        return new Github([
            'clientId' => $settings['id'],
            'clientSecret' => $settings['secret'],
            'redirectUri' => $this->router->generate('zikulausersmodule_access_login', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    }
}
