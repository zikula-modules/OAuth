<?php

/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula Foundation - http://zikula.org/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\OAuthModule;

use Zikula\Core\AbstractExtensionInstaller;

class OAuthModuleInstaller extends AbstractExtensionInstaller
{
    /**
     * @var array
     */
    private $entities = [
        'Zikula\OAuthModule\Entity\MappingEntity'
    ];

    public function install()
    {
        $this->schemaTool->create($this->entities);

        return true;
    }

    public function upgrade($oldversion)
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }
}
