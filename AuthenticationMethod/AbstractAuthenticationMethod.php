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

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Zikula\ExtensionsModule\Api\VariableApi;
use Zikula\OAuthModule\Entity\Repository\MappingRepository;
use Zikula\UsersModule\AuthenticationMethodInterface\ReEntrantAuthenticationmethodInterface;

abstract class AbstractAuthenticationMethod implements ReEntrantAuthenticationmethodInterface
{
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
     * AbstractAuthenticationMethod constructor.
     * @param Session $session
     * @param RequestStack $requestStack
     * @param RouterInterface $router
     * @param MappingRepository $repository
     * @param VariableApi $variableApi
     */
    public function __construct(Session $session, RequestStack $requestStack, RouterInterface $router, MappingRepository $repository, VariableApi $variableApi)
    {
        require_once __DIR__ . '/../vendor/autoload.php';
        $this->session = $session;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->repository = $repository;
        $this->variableApi = $variableApi;
    }

    /**
     * @return AbstractProvider
     */
    abstract protected function getProvider();

    abstract protected function getUserName();

    public function authenticate(array $data)
    {
        $provider = $this->getProvider();
        $request = $this->requestStack->getCurrentRequest();
        $state = $request->query->get('state', null);
        $code = $request->query->get('code', null);

        if (!isset($code)) {
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
                $this->session->getFlashBag()->add('success', sprintf('Hello %s!', $this->getUserName()));

                return $this->repository->getZikulaId('github', $this->user->getId());
            } catch (\Exception $e) {
                $this->session->getFlashBag()->add('error', 'Could not obtain user details from Github. (' . $e->getMessage() . ')');

                return null;
            }
        }
    }

    public function getUserData()
    {
        if (!$this->user) {
            $this->authenticate([]);
        }

        return $this->user->toArray();
    }
}
