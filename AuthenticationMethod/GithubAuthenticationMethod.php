<?php

declare(strict_types=1);
/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula Foundation - https://ziku.la/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\OAuthModule\AuthenticationMethod;

use League\OAuth2\Client\Provider\Github;
use Zikula\OAuthModule\Exception\InvalidProviderConfigException;
use Zikula\OAuthModule\OAuthConstant;

class GithubAuthenticationMethod extends AbstractAuthenticationMethod
{
    /**
     * @var string
     */
    private $email;

    public function getAlias()
    {
        return OAuthConstant::ALIAS_GITHUB;
    }

    public function getDisplayName()
    {
        return $this->translator->__('Github');
    }

    public function getDescription()
    {
        return $this->translator->__('Login using Github via OAuth.');
    }

    public function getUname()
    {
        return $this->user->getNickname();
    }

    public function getEmail()
    {
        return $this->email;
    }

    protected function getAuthorizationUrlOptions()
    {
        return [
            'state' => 'OPTIONAL_CUSTOM_CONFIGURED_STATE',
            'scope' => ['user:email']
        ];
    }

    protected function setAdditionalUserData()
    {
        // this method is needed to get the user's email because github doesn't provide it as a standard
        // part of the data that is returned as a 'user'.
        $request = $this->getProvider()->getAuthenticatedRequest(
            'GET',
            'https://api.github.com/user/emails',
            $this->token->getToken()
        );

        $emails = $this->getProvider()->getResponse($request);
        foreach ($emails as $email) {
            if ($email['primary']) {
                $this->email = $email['email'];
            }
        }
    }

    protected function setProvider($redirectUri)
    {
        $settings = $this->variableApi->get('ZikulaOAuthModule', OAuthConstant::ALIAS_GITHUB);
        if (!isset($settings['id']) || !isset($settings['secret'])) {
            throw new InvalidProviderConfigException($this->translator->__('Invalid settings for Github OAuth provider.'));
        }

        $this->provider = new Github([
            'clientId' => $settings['id'],
            'clientSecret' => $settings['secret'],
            'redirectUri' => $redirectUri
        ]);
    }
}
