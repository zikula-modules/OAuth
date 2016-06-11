<?php

namespace Zikula\OAuthModule\Container;

use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\Translator;
use Zikula\Core\LinkContainer\LinkContainerInterface;
use Zikula\PermissionsModule\Api\PermissionApi;
use Zikula\UsersModule\Collector\AuthenticationMethodCollector;

class LinkContainer implements LinkContainerInterface
{
    /**
     * @var Translator
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
     * @param $translator
     * @param RouterInterface $router
     * @param PermissionApi $permissionApi
     */
    public function __construct($translator, RouterInterface $router, PermissionApi $permissionApi, AuthenticationMethodCollector $collector)
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
        if ($this->permissionApi->hasPermission('ZikulaOAuthModule::', '::', ACCESS_READ)) {
            $methods = ['github', 'google'];
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
