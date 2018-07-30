<?php

/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula Foundation - https://ziku.la/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\OAuthModule\Container;

use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Core\LinkContainer\LinkContainerInterface;
use Zikula\OAuthModule\OAuthConstant;
use Zikula\PermissionsModule\Api\PermissionApi;
use Zikula\UsersModule\Collector\AuthenticationMethodCollector;

class LinkContainer implements LinkContainerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var PermissionApi
     */
    private $permissionApi;

    /**
     * @var AuthenticationMethodCollector
     */
    private $collector;

    /**
     * constructor.
     *
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     * @param PermissionApi $permissionApi
     * @param AuthenticationMethodCollector $collector
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router, PermissionApi $permissionApi, AuthenticationMethodCollector $collector)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->permissionApi = $permissionApi;
        $this->collector = $collector;
    }

    /**
     * get Links of any type for this extension
     * required by the interface
     *
     * @param string $type
     * @return array
     */
    public function getLinks($type = LinkContainerInterface::TYPE_ADMIN)
    {
        $method = 'get' . ucfirst(strtolower($type));
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return [];
    }

    /**
     * get the Admin links for this extension
     *
     * @return array
     */
    private function getAdmin()
    {
        $links = [];
        if ($this->permissionApi->hasPermission('ZikulaOAuthModule::', '::', ACCESS_ADMIN)) {
            $links[] = [
                'url' => $this->router->generate('zikulaoauthmodule_mapping_list'),
                'text' => $this->translator->__('Mapping list'),
                'icon' => 'list'
            ];
            $methods = [OAuthConstant::ALIAS_GITHUB, OAuthConstant::ALIAS_GOOGLE, OAuthConstant::ALIAS_FACEBOOK, OAuthConstant::ALIAS_LINKEDIN];
            foreach ($methods as $method) {
                $authMethod = $this->collector->get($method);
                $links[] = [
                    'url' => $this->router->generate('zikulaoauthmodule_config_settings', ['method' => $method]),
                    'text' => $authMethod->getDisplayName() . ' ' . $this->translator->__('settings'),
                    'icon' => 'wrench'
                ];
            }
        }

        return $links;
    }

    /**
     * set the BundleName as required buy the interface
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'ZikulaOAuthModule';
    }
}
