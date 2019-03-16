<?php

/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula Foundation - https://ziku.la/
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
use Zikula\OAuthModule\Entity\Repository\MappingRepository;

/**
 * Class MappingController
 */
class MappingController extends AbstractController
{
    /**
     * @Route("/list")
     * @Template("ZikulaOAuthModule:Mapping:list.html.twig")
     * @Theme("admin")
     *
     * @param MappingRepository $mappingRepository
     */
    public function listAction(MappingRepository $mappingRepository)
    {
        if (!$this->hasPermission('ZikulaOAuthModule', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        return [
            'mappings' => $mappingRepository->findAll()
        ];
    }
}
