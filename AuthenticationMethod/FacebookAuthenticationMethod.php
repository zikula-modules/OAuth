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

use League\OAuth2\Client\Provider\Facebook;
use Zikula\OAuthModule\Exception\InvalidProviderConfigException;
use Zikula\OAuthModule\OAuthConstant;

class FacebookAuthenticationMethod extends AbstractAuthenticationMethod
{
    public function getAlias()
    {
        return OAuthConstant::ALIAS_FACEBOOK;
    }

    public function getDisplayName()
    {
        return 'Facebook';
    }

    public function getDescription()
    {
        return 'Login using Facebook via OAuth.';
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
        $settings = $this->variableApi->get('ZikulaOAuthModule', OAuthConstant::ALIAS_FACEBOOK);
        if (!isset($settings['id']) || !isset($settings['secret'])) {
            throw new InvalidProviderConfigException('Invalid settings for Facebook OAuth provider.');
        }

        $this->provider = new Facebook([
            'clientId' => $settings['id'],
            'clientSecret' => $settings['secret'],
            'redirectUri' => $redirectUri,
            'graphApiVersion' => 'v2.9',
        ]);
    }
}
