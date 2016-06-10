<?php

namespace Zikula\OAuthModule\Container;

use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\Translator;
use Zikula\Core\LinkContainer\LinkContainerInterface;
use Zikula\PermissionsModule\Api\PermissionApi;

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
     * constructor.
     *
     * @param $translator
     * @param RouterInterface $router
     */
    public function __construct($translator, RouterInterface $router, PermissionApi $permissionApi)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->permissionApi = $permissionApi;
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
//    private function getUser()
//    {
//        $links = [];
//        if ($this->permissionApi->hasPermission('ZikulaOAuthModule::', '::', ACCESS_READ)) {
//            $links[] = [
//                'url' => $this->router->generate('zikulaoauthmodule_auth_index'),
//                'text' => $this->translator->__('Admin'),
//                'icon' => 'wrench'
//            ];
//        }
//
//        return $links;
//    }

    /**
     * get the Admin links for this extension
     *
     * @return array
     */
//    private function getAdmin()
//    {
//        $links = [];
//        if ($this->permissionApi->hasPermission('ZikulaOAuthModule::', '::', ACCESS_READ)) {
//            $links[] = [
//                'url' => $this->router->generate('zikulaoauthmodule_auth_index'),
//                'text' => $this->translator->__('Admin'),
//                'icon' => 'wrench'
//            ];
//        }
//
//        return $links;
//    }

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
