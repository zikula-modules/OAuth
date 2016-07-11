<?php

/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula Foundation - http://zikula.org/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\OAuthModule\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Controller\AbstractController;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * Class MappingController
 */
class MappingController extends AbstractController
{
    /**
     * @Route("/list")
     * @Template
     * @Theme("admin")
     */
    public function listAction()
    {
        if (!$this->hasPermission('ZikulaOAuthModule', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        $mappings = $this->get('zikula_oauth_module.mapping_repository')->findAll();

        return [
            'mappings' => $mappings
        ];
    }
}
