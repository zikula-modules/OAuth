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

namespace Zikula\OAuthModule\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Zikula\Bundle\CoreBundle\Controller\AbstractController;
use Zikula\OAuthModule\Entity\Repository\MappingRepository;
use Zikula\PermissionsModule\Annotation\PermissionCheck;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * Class MappingController
 *
 * @PermissionCheck("admin")
 */
class MappingController extends AbstractController
{
    /**
     * @Route("/list")
     * @Template("@ZikulaOAuthModule/Mapping/list.html.twig")
     * @Theme("admin")
     */
    public function listAction(MappingRepository $mappingRepository): array
    {
        return [
            'mappings' => $mappingRepository->findAll()
        ];
    }
}
