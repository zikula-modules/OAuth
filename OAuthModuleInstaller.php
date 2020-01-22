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

namespace Zikula\OAuthModule;

use Zikula\ExtensionsModule\Installer\AbstractExtensionInstaller;
use Zikula\OAuthModule\Entity\MappingEntity;

class OAuthModuleInstaller extends AbstractExtensionInstaller
{
    /**
     * @var array
     */
    private $entities = [
        MappingEntity::class
    ];

    public function install(): bool
    {
        $this->schemaTool->create($this->entities);

        return true;
    }

    public function upgrade(string $oldVersion): bool
    {
        return true;
    }

    public function uninstall(): bool
    {
        $this->schemaTool->drop($this->entities);

        return true;
    }
}
