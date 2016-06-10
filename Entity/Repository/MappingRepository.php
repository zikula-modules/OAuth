<?php
/**
 * This file is part of the Zikula package.
 *
 * Copyright Zikula Foundation - http://zikula.org/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\OAuthModule\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class MappingRepository extends EntityRepository
{
    public function getZikulaId($method, $methodId)
    {
        $mapping = parent::findOneBy(['method' => $method, 'methodId' => $methodId]);

        if (isset($mapping)) {
            return $mapping->getZikulaId();
        } else {
            return null;
        }
    }
}
