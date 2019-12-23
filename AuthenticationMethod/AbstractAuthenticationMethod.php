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

use Exception;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use LogicException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
     * @var SessionInterface
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

    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        RouterInterface $router,
        MappingRepository $repository,
        VariableApi $variableApi
    ) {
        $this->translator = $translator;
        $request = $requestStack->getCurrentRequest();
        $this->session = $request->hasSession() ? $request->getSession() : null;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->repository = $repository;
        $this->variableApi = $variableApi;
    }

    abstract protected function setProvider(string $redirectUri): void;

    public function getProvider(): AbstractProvider
    {
        return $this->provider;
    }

    protected function getAuthorizationUrlOptions(): array
    {
        return [];
    }

    /**
     * Method called during `authenticate` method after token is set.
     * Allows author to take actions which require the token.
     */
    protected function setAdditionalUserData(): void
    {
    }

    public function authenticate(array $data = []): ?int
    {
        $redirectUri = $data['redirectUri'] ?? $this->router->generate('zikulausersmodule_access_login', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->setProvider($redirectUri);
        $request = $this->requestStack->getCurrentRequest();
        $state = null !== $request ? $request->query->get('state') : null;
        $code = null !== $request ? $request->query->get('code') : null;

        if (!isset($code)) {
            // If no authorization code then get one
            $authUrl = $this->getProvider()->getAuthorizationUrl($this->getAuthorizationUrlOptions());
            if (null !== $this->session) {
                $this->session->set('oauth2state', $this->getProvider()->getState());
            }

            header('Location: ' . $authUrl);
            exit;
        }

        // Check given state against previously stored one to mitigate CSRF attack
        if (empty($state) || (null !== $this->session && $state !== $this->session->get('oauth2state'))) {
            $this->session->remove('oauth2state');
            $this->session->getFlashBag()->add('error', 'Invalid State');

            return null;
        }

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
                /*if (null !== $this->session) {
                    $this->session->getFlashBag()->add('success', sprintf('Hello %s!', $this->getUname()));
                }*/
            } else {
                $registrationUrl = $this->router->generate('zikulausersmodule_registration_register');
                if (null !== $this->session) {
                    $this->session->remove('oauth2state');
                }
                $registerLink = '<a href="' . $registrationUrl . '">' . $this->translator->__('create a new account') . '</a>';
                $errorMessage = $this->translator->__f('This user is not locally registered. You must first %registerLink on this site before logging in with %displayName', [
                    '%registerLink' => $registerLink,
                    '%displayName' => $this->getDisplayName()
                ]);
                if (null !== $this->session) {
                    $this->session->getFlashBag()->add('error', $errorMessage);
                }
            }

            return $uid;
        } catch (Exception $exception) {
            if (null !== $this->session) {
                $this->session->getFlashBag()->add(
                    'error',
                    $this->translator->__('Could not obtain user details from external service.') . ' (' . $exception->getMessage() . ')'
                );
            }

            return null;
        }
    }

    public function getId(): string
    {
        if (!$this->user) {
            throw new LogicException($this->translator->__('User must authenticate first.'));
        }

        return $this->user->getId();
    }

    public function register(array $data = []): bool
    {
        $mapping = new MappingEntity();
        $mapping->setMethod($this->getAlias());
        $mapping->setMethodId($data['id']);
        $mapping->setZikulaId($data['uid']);
        $this->repository->persistAndFlush($mapping);

        return true;
    }
}
