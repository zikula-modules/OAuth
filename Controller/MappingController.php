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

namespace Zikula\OAuthModule\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Controller\AbstractController;
use Zikula\OAuthModule\Entity\Repository\MappingRepository;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * Class MappingController
 */
class MappingController extends AbstractController
{
    /**
     * @Route("/list")
     * @Template("@ZikulaOAuthModule/Mapping/list.html.twig")
     * @Theme("admin")
     *
     * @throws AccessDeniedException
     */
    public function listAction(MappingRepository $mappingRepository): array
    {
        if (!$this->hasPermission('ZikulaOAuthModule', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        return [
            'mappings' => $mappingRepository->findAll()
        ];
    }
}
