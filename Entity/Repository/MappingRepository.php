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
use Zikula\OAuthModule\Entity\MappingEntity;

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

    public function persistAndFlush(MappingEntity $entity)
    {
        $this->_em->persist($entity);
        $this->_em->flush($entity);
    }

    public function removeByZikulaId($uid)
    {
        $mapping = parent::findOneBy(['zikulaId' => $uid]);
        if (isset($mapping)) {
            $this->_em->remove($mapping);
            $this->_em->flush();
        }
    }
}
