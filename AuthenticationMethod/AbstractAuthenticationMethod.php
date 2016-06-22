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
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Zikula\ExtensionsModule\Api\VariableApi;
use Zikula\OAuthModule\Entity\MappingEntity;
use Zikula\OAuthModule\Entity\Repository\MappingRepository;
use Zikula\UsersModule\AuthenticationMethodInterface\ReEntrantAuthenticationMethodInterface;
use Zikula\UsersModule\Entity\UserEntity;

abstract class AbstractAuthenticationMethod implements ReEntrantAuthenticationMethodInterface
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
     * @var AccessToken
     */
    protected $token;

    /**
     * @var AbstractProvider
     */
    protected $provider;

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
     * @return string
     */
    abstract protected function getUname();

    /**
     * @return string
     */
    abstract protected function getEmail();

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
     * @return integer|null if Zikula Uid is set for provider ID, this is returned, else null.
     */
    public function authenticate(array $data)
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
                $this->session->getFlashBag()->add('success', sprintf('Hello %s!', $this->getUname()));

                return $this->repository->getZikulaId($this->getAlias(), $this->user->getId());
            } catch (\Exception $e) {
                $this->session->getFlashBag()->add('error', 'Could not obtain user details from Github. (' . $e->getMessage() . ')');

                return null;
            }
        }
    }

    public function updateUserEntity(UserEntity $userEntity)
    {
        $userEntity->setEmail($this->getEmail());
        $userEntity->setUname($this->getUname());
    }

    public function getId()
    {
        if (!$this->user) {
            throw new \LogicException('User must authenticate first.');
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
    }
}
