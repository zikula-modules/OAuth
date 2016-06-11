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
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Zikula\OAuthModule\Entity\Repository\MappingRepository;
use Zikula\UsersModule\AuthenticationMethodInterface\ReEntrantAuthenticationmethodInterface;

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

    public function authenticate(array $data)
    {
        $settings = $this->variableApi->get('ZikulaOAuthModule', 'google');
        $provider = new Google([
            'clientId' => $settings['id'],
            'clientSecret' => $settings['secret'],
            'redirectUri' => $this->router->generate('zikulausersmodule_access_login', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        $request = $this->requestStack->getCurrentRequest();
        $error = $request->query->get('error', null);
        $state = $request->query->get('state', null);
        $code = $request->query->get('code', null);

        if (!empty($error)) {
            $this->session->getFlashBag()->add('error', $error);

            return null;
        } elseif (!isset($code)) {
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            $this->session->set('oauth2state', $provider->getState());

            header('Location: ' . $authUrl);
            exit;

            // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($state) || ($state !== $this->session->get('oauth2state'))) {
            $this->session->remove('oauth2state');
            $this->session->getFlashBag()->add('error', 'Invalid State');

            return null;
        } else {
            // Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $code
            ]);

            try {
                // get the user's details
                $this->user = $provider->getResourceOwner($token);
                $this->session->getFlashBag()->add('success', sprintf('Hello %s!', $this->user->getName()));

                return $this->repository->getZikulaId('google', $this->user->getId()); // note: the method argument must match the service alias
            } catch (\Exception $e) {
                $this->session->getFlashBag()->add('error', 'Could not obtain user details from Google. (' . $e->getMessage() . ')');

                return null;
            }
        }
    }
}
