// <?php

/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula Foundation - https://ziku.la/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\OAuthModule\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Controller\AbstractController;
use Zikula\OAuthModule\Form\Type\SettingsType;
use Zikula\ThemeModule\Engine\Annotation\Theme;
use Zikula\UsersModule\Collector\AuthenticationMethodCollector;

/**
 * Class ConfigController
 */
class ConfigController extends AbstractController
{
    /**
     * @Route("/settings/{method}")
     * @Theme("admin")
     * @Template("ZikulaOAuthModule:Config:settings.html.twig")
     *
     * @param Request $request
     * @param AuthenticationMethodCollector $authenticationMethodCollector
     * @param string $method
     *
     * @return array
     */
    public function settingsAction(
        Request $request,
        AuthenticationMethodCollector $authenticationMethodCollector,
        $method = 'github'
    ) {
        if (!$this->hasPermission('ZikulaOAuthModule', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(SettingsType::class, $this->getVar($method, []));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('save')->isClicked()) {
                $this->setVar($method, $form->getData());
                $this->addFlash('success', $this->__f('Settings for %method saved!', ['%method' => $method]));
            }
        }

        return [
            'form' => $form->createView(),
            'method' => $authenticationMethodCollector->get($method)
        ];
    }
}
