<?php

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
