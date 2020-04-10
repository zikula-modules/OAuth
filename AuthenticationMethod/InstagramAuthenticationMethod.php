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

use League\OAuth2\Client\Provider\Instagram;
use Zikula\OAuthModule\Exception\InvalidProviderConfigException;
use Zikula\OAuthModule\OAuthConstant;

class InstagramAuthenticationMethod extends AbstractAuthenticationMethod
{
    public function getAlias(): string
    {
        return OAuthConstant::ALIAS_INSTAGRAM;
    }

    public function getDisplayName(): string
    {
        return $this->translator->trans('Instagram');
    }

    public function getDescription(): string
    {
        return $this->translator->trans('Login using Instagram via OAuth.');
    }

    public function getUname(): string
    {
        return $this->user->getName();
    }

    public function getEmail(): string
    {
        return $this->user->getEmail();
    }

    protected function setProvider(string $redirectUri): void
    {
        $settings = $this->variableApi->get('ZikulaOAuthModule', OAuthConstant::ALIAS_INSTAGRAM);
        if (!isset($settings['id'], $settings['secret'])) {
            throw new InvalidProviderConfigException($this->translator->trans('Invalid settings for Instagram OAuth provider.'));
        }

        $this->provider = new Instagram([
            'clientId' => $settings['id'],
            'clientSecret' => $settings['secret'],
            'redirectUri' => $redirectUri
        ]);
    }
}
