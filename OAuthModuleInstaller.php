<?php

namespace Zikula\OAuthModule;

use Zikula\Core\AbstractBundle;
use Zikula\Core\AbstractExtensionInstaller;

class OAuthModuleInstaller extends AbstractExtensionInstaller
{
    /**
     * @var array
     */
    private $entities = [
        'Zikula\OAuthModule\Entity\Mapping'
    ];

    public function setBundle(AbstractBundle $bundle)
    {
        $this->bundle = $bundle;
    }

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
