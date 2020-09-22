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

namespace Zikula\OAuthModule\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Zikula\MenuModule\ExtensionMenu\ExtensionMenuInterface;
use Zikula\OAuthModule\OAuthConstant;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;
use Zikula\UsersModule\Collector\AuthenticationMethodCollector;

class ExtensionMenu implements ExtensionMenuInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var PermissionApiInterface
     */
    private $permissionApi;

    /**
     * @var AuthenticationMethodCollector
     */
    private $collector;

    public function __construct(
        FactoryInterface $factory,
        PermissionApiInterface $permissionApi,
        AuthenticationMethodCollector $collector
    ) {
        $this->factory = $factory;
        $this->permissionApi = $permissionApi;
        $this->collector = $collector;
    }

    public function get(string $type = self::TYPE_ADMIN): ?ItemInterface
    {
        $method = 'get' . ucfirst(mb_strtolower($type));
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        return null;
    }

    private function getAdmin(): ?ItemInterface
    {
        $menu = $this->factory->createItem('oauthAdminMenu');
        if ($this->permissionApi->hasPermission('ZikulaOAuthModule::', '::', ACCESS_ADMIN)) {
            $menu->addChild('Mapping list', [
                'route' => 'zikulaoauthmodule_mapping_listmappings',
            ])->setAttribute('icon', 'fas fa-list');

            $menu->addChild('Provider settings', [
                'uri' => '#'
            ])
                ->setAttribute('icon', 'fas fa-wrench')
                ->setAttribute('dropdown', true)
            ;

            $methods = [
                OAuthConstant::ALIAS_GITHUB,
                OAuthConstant::ALIAS_FACEBOOK,
                OAuthConstant::ALIAS_GOOGLE,
                OAuthConstant::ALIAS_INSTAGRAM,
                OAuthConstant::ALIAS_LINKEDIN
            ];
            foreach ($methods as $method) {
                $authMethod = $this->collector->get($method);
                if (null === $authMethod) {
                    continue;
                }
                $menu['Provider settings']->addChild($method, [
                    'route' => 'zikulaoauthmodule_config_settings',
                    'routeParameters' => ['method' => $method]
                ])
                    ->setLabel('%auth_method% settings')
                    ->setExtra('translation_params', ['%auth_method%' => $authMethod->getDisplayName()])
                    ->setAttribute('icon', 'fab fa-' . $method)
                ;
            }
        }

        return 0 === $menu->count() ? null : $menu;
    }

    public function getBundleName(): string
    {
        return 'ZikulaOAuthModule';
    }
}
