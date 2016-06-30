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
        return 'Github';
    }

    public function getDescription()
    {
        return 'Login using Github via OAuth.';
    }

    protected function getUname()
    {
        return $this->user->getNickname();
    }

    protected function getEmail()
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
        $settings = $this->variableApi->get('ZikulaOAuthModule', 'github');
        if (!isset($settings['id']) || !isset($settings['secret'])) {
            throw new InvalidProviderConfigException('Invalid settings for Github OAuth provider.');
        }

        $this->provider = new Github([
            'clientId' => $settings['id'],
            'clientSecret' => $settings['secret'],
            'redirectUri' => $redirectUri,
        ]);
    }
}
