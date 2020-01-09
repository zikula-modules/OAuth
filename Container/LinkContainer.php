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

namespace Zikula\OAuthModule\Container;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
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

    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        PermissionApi $permissionApi,
        AuthenticationMethodCollector $collector
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->permissionApi = $permissionApi;
        $this->collector = $collector;
    }

    public function getLinks(string $type = LinkContainerInterface::TYPE_ADMIN): array
    {
        $method = 'get' . ucfirst(mb_strtolower($type));
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        return [];
    }

    /**
     * Get the admin links for this extension.
     */
    private function getAdmin(): array
    {
        $links = [];
        if ($this->permissionApi->hasPermission('ZikulaOAuthModule::', '::', ACCESS_ADMIN)) {
            $links[] = [
                'url' => $this->router->generate('zikulaoauthmodule_mapping_list'),
                'text' => $this->translator->trans('Mapping list'),
                'icon' => 'list'
            ];
            $methods = [OAuthConstant::ALIAS_GITHUB, OAuthConstant::ALIAS_GOOGLE, OAuthConstant::ALIAS_FACEBOOK, OAuthConstant::ALIAS_LINKEDIN];
            foreach ($methods as $method) {
                $authMethod = $this->collector->get($method);
                if (null === $authMethod) {
                    continue;
                }
                $links[] = [
                    'url' => $this->router->generate('zikulaoauthmodule_config_settings', ['method' => $method]),
                    'text' => $authMethod->getDisplayName() . ' ' . $this->translator->trans('settings'),
                    'icon' => 'wrench'
                ];
            }
        }

        return $links;
    }

    public function getBundleName(): string
    {
        return 'ZikulaOAuthModule';
    }
}
