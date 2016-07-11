<?php

/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula Foundation - http://zikula.org/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\OAuthModule\AuthenticationMethod;

use League\OAuth2\Client\Provider\Google;
use Zikula\OAuthModule\Exception\InvalidProviderConfigException;
use Zikula\OAuthModule\OAuthConstant;

class GoogleAuthenticationMethod extends AbstractAuthenticationMethod
{
    public function getAlias()
    {
        return OAuthConstant::ALIAS_GOOGLE;
    }

    public function getDisplayName()
    {
        return 'Google';
    }

    public function getDescription()
    {
        return 'Login using Google via OAuth.';
    }

    public function getUname()
    {
        return $this->user->getName();
    }

    public function getEmail()
    {
        return $this->user->getEmail();
    }

    protected function setProvider($redirectUri)
    {
        $settings = $this->variableApi->get('ZikulaOAuthModule', 'google');
        if (!isset($settings['id']) || !isset($settings['secret'])) {
            throw new InvalidProviderConfigException('Invalid settings for Google OAuth provider.');
        }

        $this->provider = new Google([
            'clientId' => $settings['id'],
            'clientSecret' => $settings['secret'],
            'redirectUri' => $redirectUri,
        ]);
    }
}
