<?php

/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula Foundation - https://ziku.la/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\OAuthModule\AuthenticationMethod;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\ExtensionsModule\Api\VariableApi;
use Zikula\OAuthModule\Entity\MappingEntity;
use Zikula\OAuthModule\Entity\Repository\MappingRepository;
use Zikula\UsersModule\AuthenticationMethodInterface\ReEntrantAuthenticationMethodInterface;

abstract class AbstractAuthenticationMethod implements ReEntrantAuthenticationMethodInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var ResourceOwnerInterface
     */
    protected $user;

    /**
     * @var MappingRepository
     */
    protected $repository;

    /**
     * @var VariableApi
     */
    protected $variableApi;

    /**
     * @var AccessToken
     */
    protected $token;

    /**
     * @var AbstractProvider
     */
    protected $provider;

    /**
     * AbstractAuthenticationMethod constructor.
     *
     * @param TranslatorInterface $translator
     * @param RequestStack $requestStack
     * @param RouterInterface $router
     * @param MappingRepository $repository
     * @param VariableApi $variableApi
     */
    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        RouterInterface $router,
        MappingRepository $repository,
        VariableApi $variableApi
    ) {
        $this->translator = $translator;
        $this->session = $requestStack->getCurrentRequest()->getSession();
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->repository = $repository;
        $this->variableApi = $variableApi;
    }

    /**
     * @param string $redirectUri
     */
    abstract protected function setProvider($redirectUri);

    /**
     * @return AbstractProvider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @return array
     */
    protected function getAuthorizationUrlOptions()
    {
        return [];
    }

    /**
     * Method called during `authenticate` method after token is set.
     * Allows author to take actions which require the token.
     */
    protected function setAdditionalUserData()
    {
    }

    /**
     * Authenticate the user to the provider.
     * @param array $data
     * @return integer|null if Zikula Uid is set for provider ID, this is returned, else null
     */
    public function authenticate(array $data = [])
    {
        $redirectUri = isset($data['redirectUri']) ? $data['redirectUri'] : $this->router->generate('zikulausersmodule_access_login', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->setProvider($redirectUri);
        $request = $this->requestStack->getCurrentRequest();
        $state = $request->query->get('state', null);
        $code = $request->query->get('code', null);

        if (!isset($code)) {
            // If no authorization code then get one
            $authUrl = $this->getProvider()->getAuthorizationUrl($this->getAuthorizationUrlOptions());
            $this->session->set('oauth2state', $this->getProvider()->getState());

            header('Location: ' . $authUrl);
            exit;

        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($state) || ($state !== $this->session->get('oauth2state'))) {
            $this->session->remove('oauth2state');
            $this->session->getFlashBag()->add('error', 'Invalid State');

            return null;
        } else {
            // Try to get an access token (using the authorization code grant)
            $this->token = $this->getProvider()->getAccessToken('authorization_code', [
                'code' => $code
            ]);

            try {
                // get the user's details
                $this->user = $this->getProvider()->getResourceOwner($this->token);
                $this->setAdditionalUserData();
                $uid = $this->repository->getZikulaId($this->getAlias(), $this->user->getId());
                if (isset($uid)) {
                    //$this->session->getFlashBag()->add('success', sprintf('Hello %s!', $this->getUname()));
                } else {
                    $registrationUrl = $this->router->generate('zikulausersmodule_registration_register');
                    $this->session->remove('oauth2state');
                    $registerLink = '<a href="' . $registrationUrl . '">' . $this->translator->__('create a new account') . '</a>';
                    $this->session->getFlashBag()->add('error',
                        $this->translator->__f(
                            'This user is not locally registered. You must first %registerLink on this site before logging in with %displayName', [
                                '%registerLink' => $registerLink,
                                '%displayName' => $this->getDisplayName()
                            ]
                        )
                    );
                }

                return $uid;
            } catch (\Exception $exception) {
                $this->session->getFlashBag()->add('error', $this->translator->__('Could not obtain user details from external service.') . ' (' . $exception->getMessage() . ')');

                return null;
            }
        }
    }

    public function getId()
    {
        if (!$this->user) {
            throw new \LogicException($this->translator->__('User must authenticate first.'));
        }

        return $this->user->getId();
    }

    public function register(array $data)
    {
        $mapping = new MappingEntity();
        $mapping->setMethod($this->getAlias());
        $mapping->setMethodId($data['id']);
        $mapping->setZikulaId($data['uid']);
        $this->repository->persistAndFlush($mapping);

        return true;
    }
}
