<?php

declare(strict_types=1);
/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula - https://ziku.la/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\OAuthModule\AuthenticationMethod;

use League\OAuth2\Client\Provider\LinkedIn;
use Zikula\OAuthModule\Exception\InvalidProviderConfigException;
use Zikula\OAuthModule\OAuthConstant;

class LinkedInAuthenticationMethod extends AbstractAuthenticationMethod
{
    public function getAlias(): string
    {
        return OAuthConstant::ALIAS_LINKEDIN;
    }

    public function getDisplayName(): string
    {
        return $this->translator->trans('LinkedIn');
    }

    public function getDescription(): string
    {
        return $this->translator->trans('Login using LinkedIn via OAuth.');
    }

    public function getUname(): string
    {
        return $this->user->getFirstname() . ' ' . $this->user->getLastName();
    }

    public function getEmail(): string
    {
        return $this->user->getEmail();
    }

    protected function setProvider(string $redirectUri): void
    {
        $settings = $this->variableApi->get('ZikulaOAuthModule', OAuthConstant::ALIAS_LINKEDIN);
        if (!isset($settings['id'], $settings['secret'])) {
            throw new InvalidProviderConfigException($this->translator->trans('Invalid settings for LinkedIn OAuth provider.'));
        }

        $this->provider = new LinkedIn([
            'clientId' => $settings['id'],
            'clientSecret' => $settings['secret'],
            'redirectUri' => $redirectUri
        ]);
    }
}
